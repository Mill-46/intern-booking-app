<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'service_date' => ['required', 'date'],
            'service_type' => ['required', 'string', 'max:255'],
            'workshop_name' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
