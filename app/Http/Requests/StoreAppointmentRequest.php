<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'service_type' => ['required', Rule::in([
        Appointment::SERVICE_NEW_ABAYA,
        Appointment::SERVICE_ALTERATION,
        Appointment::SERVICE_REPAIR,
      ])],
      'appointment_date' => [
        'required',
        'date',
        'after:today',
        'before:+1 month',
        function ($attribute, $value, $fail) {
          $date = Carbon::parse($value);

          // Check if appointment is during business hours (9 AM to 5 PM)
          if ($date->hour < 9 || $date->hour >= 17) {
            $fail('Appointments must be scheduled between 9 AM and 5 PM.');
          }

          // Check if appointment is on a weekend
          if ($date->isWeekend()) {
            $fail('Appointments cannot be scheduled on weekends.');
          }
        },
      ],
      'phone' => ['required', 'string', 'max:20'],
      'notes' => ['nullable', 'string', 'max:500'],
    ];
  }

  public function messages(): array
  {
    return [
      'service_type.in' => 'Please select a valid service type.',
      'appointment_date.after' => 'Appointments must be scheduled at least one day in advance.',
      'appointment_date.before' => 'Appointments cannot be scheduled more than one month in advance.',
    ];
  }
}
