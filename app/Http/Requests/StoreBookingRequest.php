<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'origin_site_id' => ['required', 'exists:sites,id'],
            'destination_site_id' => ['required', 'exists:sites,id', 'different:origin_site_id'],
            'approver_l1_id' => ['required', 'exists:users,id'],
            'approver_l2_id' => ['required', 'exists:users,id', 'different:approver_l1_id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'destination' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'destination' => trim(strip_tags((string) $this->input('destination', ''))),
            'purpose' => trim(strip_tags((string) $this->input('purpose', ''))),
        ]);
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $approverL1 = \App\Models\User::find($this->integer('approver_l1_id'));
                $approverL2 = \App\Models\User::find($this->integer('approver_l2_id'));

                if ($approverL1 && $approverL1->role !== \App\Models\User::ROLE_APPROVER_L1) {
                    $validator->errors()->add('approver_l1_id', 'Selected user must be approver level 1.');
                }

                if ($approverL2 && $approverL2->role !== \App\Models\User::ROLE_APPROVER_L2) {
                    $validator->errors()->add('approver_l2_id', 'Selected user must be approver level 2.');
                }
            },
        ];
    }
}
