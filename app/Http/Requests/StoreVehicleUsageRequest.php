<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
            'origin_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'destination_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'odometer_start' => ['required', 'integer', 'min:0'],
            'odometer_end' => ['required', 'integer', 'gte:odometer_start'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
