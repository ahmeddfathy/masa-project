<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Notifications\AppointmentStatusUpdated;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['user', 'user.phoneNumbers', 'user.addresses'])
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('appointment_date', $request->date);
        }

        // Filter by service type
        if ($request->service_type) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by reference number
        if ($request->reference) {
            $query->where('reference_number', 'like', '%' . strtoupper($request->reference) . '%');
        }

        // Search by customer
        if ($request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $appointments = $query->paginate(10);

        return view('admin.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'user.phoneNumbers', 'user.addresses', 'orderItems.order']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_APPROVED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_REJECTED
            ]),
            'notes' => 'nullable|string|max:500'
        ]);

        $appointment->update($validated);

        // Notify the customer
        $appointment->user->notify(new AppointmentStatusUpdated($appointment));

        return redirect()->route('admin.appointments.show', $appointment->reference_number)
            ->with('success', 'تم تحديث حالة الموعد بنجاح');
    }

    public function updateDateTime(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500'
        ]);

        $appointment->update($validated);

        // Notify the customer
        $appointment->user->notify(new AppointmentStatusUpdated($appointment));

        return redirect()->route('admin.appointments.show', $appointment->reference_number)
            ->with('success', 'تم تحديث موعد الزيارة بنجاح.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
