<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicle = $this->route('vehicle');

        return [
            'registration_no' => ['required', 'string', 'max:255', Rule::unique('vehicles', 'registration_no')->ignore($vehicle)],
            'vehicle_type' => ['required', 'in:person,cargo'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'fuel_capacity' => ['nullable', 'numeric', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:available,maintenance,rented'],
            'owner' => ['required', 'in:company,rental'],
        ];
    }
}
