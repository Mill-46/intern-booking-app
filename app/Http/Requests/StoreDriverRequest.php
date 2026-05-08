<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'license_no' => ['required', 'string', 'max:255', 'unique:drivers,license_no'],
            'license_expiry' => ['required', 'date'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
