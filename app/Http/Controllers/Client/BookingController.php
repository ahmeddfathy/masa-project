<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageAddon;
use App\Models\Service;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();
        $addons = PackageAddon::where('is_active', true)->get();
        $gallery_images = Gallery::latest()->take(5)->get();

        $oldFormData = session('booking_form_data', []);
        if (!empty($oldFormData)) {
            foreach ($oldFormData as $key => $value) {
                session()->flash("_old_input.{$key}", $value);
            }
            session()->forget('booking_form_data');
        }

        return view('client.booking.index', compact(
            'services',
            'packages',
            'addons',
            'gallery_images'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'package_id' => 'required|exists:packages,id',
            'session_date' => 'required|date|after:today',
            'session_time' => 'required',
            'baby_name' => 'nullable|string|max:100',
            'baby_birth_date' => 'nullable|date',
            'gender' => 'nullable|in:ذكر,أنثى',
            'notes' => 'nullable|string',
            'addons' => 'nullable|array',
            'image_consent' => 'required|in:0,1',
            'terms_consent' => 'required|accepted'
        ]);

        $package = Package::findOrFail($validated['package_id']);
        $total_amount = $package->base_price;

        $addons = [];
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addonData) {
                if (isset($addonData['id'])) {
                    $addon = PackageAddon::findOrFail($addonData['id']);
                    $quantity = $addonData['quantity'] ?? 1;
                    $total_amount += ($addon->price * $quantity);
                    $addons[] = [
                        'id' => $addon->id,
                        'quantity' => $quantity,
                        'price' => $addon->price
                    ];
                }
            }
        }

        $payment_id = 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

        $bookingData = array_merge($validated, [
            'user_id' => Auth::id(),
            'total_amount' => $total_amount,
            'addons' => $addons,
            'payment_id' => $payment_id
        ]);
        session(['pending_booking' => $bookingData]);

        $user = Auth::user();
        $paymentData = [
            "profile_id" => config('services.paytabs.profile_id'),
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => $payment_id,
            "cart_description" => "Photography Session - " . $package->name,
            "cart_currency" => config('services.paytabs.currency'),
            "cart_amount" => $total_amount,
            "callback" => route('client.bookings.payment.callback'),
            "return" => route('client.bookings.payment.return'),
            "customer_details" => [
                "name" => $user->name,
                "email" => $user->email,
                "phone" => $user->phone,
                "street1" => "Client Address",
                "city" => "City",
                "state" => "State",
                "country" => "EG",
                "zip" => "00000"
            ],
            "hide_shipping" => true,
            "framed" => true,
            "is_sandbox" => config('services.paytabs.is_sandbox'),
            "is_hosted" => true
        ];

        $maxRetries = 3;
        $attempt = 1;
        $lastError = null;

        while ($attempt <= $maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => config('services.paytabs.server_key'),
                        'Content-Type' => 'application/json'
                    ])
                    ->withOptions([
                        'verify' => !app()->environment('local'),
                        'connect_timeout' => 30
                    ])
                    ->post('https://secure-egypt.paytabs.com/payment/request', $paymentData);

                if ($response->successful() && isset($response['redirect_url'])) {
                    session(['payment_transaction_id' => $response['tran_ref'] ?? null]);
                    return redirect($response['redirect_url']);
                }

                if ($response->failed()) {
                    $lastError = [
                        'status' => $response->status(),
                        'body' => $response->json() ?? $response->body(),
                    ];
                }
            } catch (\Exception $e) {
                $lastError = [
                    'message' => $e->getMessage(),
                    'type' => get_class($e)
                ];
            }

            if ($attempt < $maxRetries) {
                sleep(pow(2, $attempt - 1));
            }
            $attempt++;
        }

        session(['booking_form_data' => $request->all()]);
        return redirect()->route('client.bookings.create')
            ->with('error', 'Payment gateway connection failed. Please try again later or contact support.');
    }

    public function paymentCallback(Request $request)
    {
        $paymentData = $this->extractPaymentData($request);

        if ($paymentData['tranRef']) {
            $paymentData = $this->queryPayTabsStatus($paymentData);
        }

        $bookingData = session('pending_booking');
        if (!$bookingData) {
            return redirect()->route('client.bookings.create')
                ->with('error', 'Payment error - No booking data found');
        }

        $existingBooking = $this->findExistingBooking($paymentData);
        if ($existingBooking) {
            return $this->handleExistingBooking($existingBooking, $paymentData);
        }

        if (!$paymentData['isSuccessful'] && !$paymentData['isPending']) {
            return $this->handleFailedPayment($paymentData);
        }

        return $this->createNewBooking($bookingData, $paymentData);
    }

    private function extractPaymentData(Request $request)
    {
        $paymentData = [
            'status' => $request->input('respStatus') ?? $request->input('status'),
            'tranRef' => $request->input('tran_ref') ?? session('payment_transaction_id'),
            'paymentId' => $request->input('payment_id') ?? session('pending_booking.payment_id'),
            'message' => $request->input('respMessage') ?? $request->input('message'),
            'amount' => null,
            'currency' => 'EGP'
        ];

        return $paymentData;
    }

    private function queryPayTabsStatus(array $paymentData)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => config('services.paytabs.server_key'),
                    'Content-Type' => 'application/json'
                ])
                ->post('https://secure-egypt.paytabs.com/payment/query', [
                    'profile_id' => config('services.paytabs.profile_id'),
                    'tran_ref' => $paymentData['tranRef']
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $paymentData['status'] = $result['payment_result']['response_status'] ?? $paymentData['status'];
                $paymentData['message'] = $result['payment_result']['response_message'] ?? $paymentData['message'];
                $paymentData['amount'] = $result['tran_total'] ?? null;
                $paymentData['currency'] = $result['tran_currency'] ?? 'EGP';
            }
        } catch (\Exception $e) {
            // Silent catch - no logging needed for query failures
        }

        $paymentData['isSuccessful'] = in_array($paymentData['status'], [
            'A', 'H', 'P', 'V', 'success', 'SUCCESS', '1', 1, 'CAPTURED',
            '100', 'Authorised', 'Captured', 'Approved'
        ], true);

        $paymentData['isPending'] = in_array($paymentData['status'], [
            'PENDING', 'pending', 'H', 'P', '2', 'PROCESSING'
        ], true);

        return $paymentData;
    }

    private function findExistingBooking(array $paymentData)
    {
        if (!$paymentData['tranRef'] && !$paymentData['paymentId']) {
            return null;
        }

        return Booking::where(function($query) use ($paymentData) {
            if ($paymentData['tranRef']) {
                $query->where('payment_transaction_id', $paymentData['tranRef']);
            }
            if ($paymentData['paymentId']) {
                $query->orWhere('payment_id', $paymentData['paymentId']);
            }
        })->first();
    }

    private function handleExistingBooking(Booking $booking, array $paymentData)
    {
        if ($paymentData['isSuccessful'] && $booking->status !== 'confirmed') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => $paymentData['status']
            ]);
        }

        return redirect()->route('client.bookings.success', $booking)
            ->with('success', 'Payment confirmed successfully!');
    }

    private function handleFailedPayment(array $paymentData)
    {
        session()->forget(['pending_booking', 'payment_transaction_id']);
        return redirect()->route('client.bookings.create')
            ->with('error', 'Payment failed: ' . ($paymentData['message'] ?: 'Unknown error'));
    }

    private function createNewBooking(array $bookingData, array $paymentData)
    {
        try {
            // تنظيف البيانات قبل الإنشاء
            $bookingData = array_merge($bookingData, [
                'payment_transaction_id' => $paymentData['tranRef'],
                'payment_id' => $paymentData['paymentId'],
                'payment_status' => $paymentData['status'],
                'status' => $paymentData['isSuccessful'] ? 'confirmed' :
                    ($paymentData['isPending'] ? 'pending' : 'failed'),
                'booking_date' => now()
            ]);

            // إزالة البيانات غير المطلوبة من مصفوفة الإدخال
            $addons = $bookingData['addons'] ?? [];
            unset($bookingData['addons']);

            // إنشاء الحجز
            $booking = Booking::create($bookingData);

            if (!empty($addons)) {
                foreach ($addons as $addon) {
                    $booking->addons()->attach($addon['id'], [
                        'quantity' => $addon['quantity'],
                        'price_at_booking' => $addon['price']
                    ]);
                }
            }

            session()->forget(['pending_booking', 'payment_transaction_id']);

            $message = $paymentData['isSuccessful'] ?
                'Payment confirmed successfully!' :
                'Booking created, verifying payment status...';

            return redirect()->route('client.bookings.success', $booking)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('client.bookings.create')
                ->with('error', 'عذراً، حدث خطأ أثناء إنشاء الحجز. الرجاء الاتصال بالدعم الفني.');
        }
    }

    public function paymentReturn(Request $request)
    {
        return $this->paymentCallback($request);
    }

    public function success(Booking $booking)
    {
        return view('client.booking.success', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['service', 'package', 'addons'])
            ->latest()
            ->paginate(10);
        return view('client.booking.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['service', 'package', 'addons']);
        return view('client.booking.show', compact('booking'));
    }

    public function saveFormData(Request $request)
    {
        $formData = $request->all();
        $formData['image_consent'] = $request->input('image_consent', '0');
        $formData['terms_consent'] = $request->has('terms_consent');

        session(['booking_form_data' => $formData]);

        return redirect()->route($request->query('redirect', 'register'));
    }
}
