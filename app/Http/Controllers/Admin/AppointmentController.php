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
        $query = Appointment::with('user')
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->date) {
            $query->whereDate('appointment_date', $request->date);
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
        $appointment->load(['user', 'orderItems.order']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                Appointment::STATUS_APPROVED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED
            ]),
            'notes' => 'nullable|string|max:500'
        ]);

        $appointment->update($validated);

        // Notify the customer
        $appointment->user->notify(new AppointmentStatusUpdated($appointment));

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment status updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
