<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $driver = $this->route('driver');

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'license_no' => ['required', 'string', 'max:255', Rule::unique('drivers', 'license_no')->ignore($driver)],
            'license_expiry' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
