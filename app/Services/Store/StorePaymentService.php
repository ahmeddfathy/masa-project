<?php

namespace App\Services\Store;

use App\Models\Order;
use App\Models\User;
use App\Services\Payment\StorePaytabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StorePaymentService
{
    /**
     * PayTabs service
     */
    protected $paytabsService;

    /**
     * Create a new instance of the payment service
     */
    public function __construct(StorePaytabsService $paytabsService)
    {
        $this->paytabsService = $paytabsService;
    }

    /**
     * Prepare payment data and create a new payment request
     *
     * @param array $orderData Order data
     * @param float $amount Payment amount
     * @param User $user User details
     * @return array Payment request creation results
     */
    public function initiatePayment(array $orderData, float $amount, User $user): array
    {
        // Generate a unique payment ID
        $paymentId = $orderData['payment_id'] ?? 'ORDER-' . strtoupper(Str::random(8)) . '-' . time();
        $orderData['payment_id'] = $paymentId;

        // Prepare customer data
        $customerData = $this->paytabsService->prepareCustomerDetails([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $orderData['phone'] ?? $user->phone,
            'address' => $orderData['shipping_address'] ?? $user->address,
            'city' => $user->city ?? null,
            'state' => $user->state ?? null,
            'country' => 'SA'
        ]);

        // Create description for the transaction
        $orderData['description'] = 'Order Payment - ' . $paymentId;

        // Create a new payment request via PayTabs
        $response = $this->paytabsService->createPaymentRequest($orderData, $amount, $customerData);

        if (!empty($response['success']) && !empty($response['data']['redirect_url'])) {
            // Save transaction ID in session
            session(['payment_transaction_id' => $response['data']['tran_ref'] ?? null]);

            return [
                'success' => true,
                'redirect_url' => $response['data']['redirect_url'],
                'transaction_id' => $response['data']['tran_ref'] ?? null,
                'payment_id' => $paymentId
            ];
        }

        // Prepare error message
        $error = $response['error'] ?? ['message' => 'Failed to connect to payment gateway'];
        Log::error('Failed to initiate payment', [
            'order_data' => $orderData,
            'amount' => $amount,
            'user_id' => $user->id,
            'error' => $error
        ]);

        return [
            'success' => false,
            'message' => $error['message'] ?? 'Payment gateway connection failed',
            'error' => $error
        ];
    }

    /**
     * Process payment response from payment gateway
     *
     * @param Request $request Current request
     * @return array Payment processing results
     */
    public function processPaymentResponse(Request $request): array
    {
        // Extract payment data
        $paymentData = $this->paytabsService->extractPaymentData($request);

        // Verify payment status
        if ($paymentData['tranRef']) {
            $paymentData = $this->paytabsService->verifyPaymentStatus($paymentData);
        }

        return $paymentData;
    }

    /**
     * Find existing order using payment ID or transaction reference
     *
     * @param array $paymentData Payment data
     * @return Order|null Existing order or null
     */
    public function findExistingOrder(array $paymentData): ?Order
    {
        if (empty($paymentData['tranRef']) && empty($paymentData['paymentId'])) {
            return null;
        }

        return Order::where(function($query) use ($paymentData) {
            if (!empty($paymentData['tranRef'])) {
                $query->where('payment_transaction_id', $paymentData['tranRef']);
            }
            if (!empty($paymentData['paymentId'])) {
                $query->orWhere('payment_id', $paymentData['paymentId']);
            }
        })->first();
    }

    /**
     * Update order status based on payment result
     *
     * @param Order $order Order
     * @param array $paymentData Payment data
     * @return Order Updated order
     */
    public function updateOrderPaymentStatus(Order $order, array $paymentData): Order
    {
        if ($paymentData['isSuccessful']) {
            $order->update([
                'order_status' => Order::ORDER_STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'payment_transaction_id' => $paymentData['tranRef'] ?? $order->payment_transaction_id,
                'amount_paid' => $paymentData['amount'] ?? $order->total_amount
            ]);
        } elseif ($paymentData['isPending']) {
            $order->update([
                'order_status' => Order::ORDER_STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'payment_transaction_id' => $paymentData['tranRef'] ?? $order->payment_transaction_id
            ]);
        } else {
            $order->update([
                'order_status' => Order::ORDER_STATUS_FAILED,
                'payment_status' => Order::PAYMENT_STATUS_FAILED,
                'payment_transaction_id' => $paymentData['tranRef'] ?? $order->payment_transaction_id
            ]);
        }

        return $order;
    }

    /**
     * Create a new order from payment data
     *
     * @param array $orderData Order data
     * @param array $paymentData Payment data
     * @return Order New order
     */
    public function createOrderFromPayment(array $orderData, array $paymentData): Order
    {
        $paymentStatus = $paymentData['isSuccessful'] ? Order::PAYMENT_STATUS_PAID :
                         ($paymentData['isPending'] ? Order::PAYMENT_STATUS_PENDING : Order::PAYMENT_STATUS_FAILED);

        $orderStatus = $paymentData['isSuccessful'] ? Order::ORDER_STATUS_PROCESSING :
                       ($paymentData['isPending'] ? Order::ORDER_STATUS_PENDING : Order::ORDER_STATUS_FAILED);

        $orderParams = [
            'user_id' => $orderData['user_id'],
            'total_amount' => $orderData['total_amount'],
            'shipping_address' => $orderData['shipping_address'],
            'phone' => $orderData['phone'],
            'payment_method' => 'online',
            'payment_status' => $paymentStatus,
            'order_status' => $orderStatus,
            'notes' => $orderData['notes'] ?? null,
            'policy_agreement' => true,
            'payment_transaction_id' => $paymentData['tranRef'] ?? null,
            'payment_id' => $paymentData['paymentId'] ?? $orderData['payment_id'] ?? null,
            'amount_paid' => $paymentData['isSuccessful'] ? ($paymentData['amount'] ?? $orderData['total_amount']) : 0
        ];

        // Create the order
        $order = Order::create($orderParams);

        // Add order items
        if (!empty($orderData['items'])) {
            foreach ($orderData['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                    'appointment_id' => $item['appointment_id'] ?? null,
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null
                ]);

                // Update appointment if exists
                if (!empty($item['appointment_id'])) {
                    $appointment = \App\Models\Appointment::find($item['appointment_id']);
                    if ($appointment) {
                        $appointment->update([
                            'status' => $paymentData['isSuccessful'] ?
                                \App\Models\Appointment::STATUS_APPROVED :
                                \App\Models\Appointment::STATUS_PENDING,
                            'order_item_id' => $order->items()->where('product_id', $item['product_id'])->first()->id
                        ]);
                    }
                }
            }
        }

        return $order;
    }
}
