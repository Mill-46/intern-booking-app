<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFuelConsumptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'fuel_used' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'recorded_at' => ['required', 'date'],
        ];
    }
}
