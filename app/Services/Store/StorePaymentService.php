<?php

namespace App\Services\Store;

use App\Models\Order;
use App\Models\User;
use App\Services\Payment\StoreTabbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StorePaymentService
{
    protected $tabbyService;

    public function __construct(StoreTabbyService $tabbyService)
    {
        $this->tabbyService = $tabbyService;
    }

    public function initiatePayment(array $orderData, float $amount, User $user): array
    {
        $paymentId = $orderData['payment_id'] ?? 'ORDER-' . strtoupper(Str::random(8)) . '-' . time();
        $orderData['payment_id'] = $paymentId;

        $customerData = $this->tabbyService->prepareCustomerDetails([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $orderData['phone'] ?? $user->phone,
            'address' => $orderData['shipping_address'] ?? $user->address,
            'city' => $user->city ?? null,
            'state' => $user->state ?? null,
            'country' => 'SA'
        ]);

        $orderData['description'] = 'Order Payment - ' . $paymentId;

        $response = $this->tabbyService->createPaymentRequest($orderData, $amount, $customerData);

        if (!empty($response['success']) && !empty($response['data']['redirect_url'])) {
            session(['payment_transaction_id' => $response['data']['tran_ref'] ?? null]);

            return [
                'success' => true,
                'redirect_url' => $response['data']['redirect_url'],
                'transaction_id' => $response['data']['tran_ref'] ?? null,
                'payment_id' => $paymentId
            ];
        }

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

    public function processPaymentResponse(Request $request): array
    {
        $paymentData = $this->tabbyService->extractPaymentData($request);

        if ($paymentData['tranRef']) {
            $paymentData = $this->tabbyService->verifyPaymentStatus($paymentData);
        }

        return $paymentData;
    }

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

        $order = Order::create($orderParams);

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
