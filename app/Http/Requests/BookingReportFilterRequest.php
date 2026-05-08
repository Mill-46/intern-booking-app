<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingReportFilterRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'status' => ['nullable', Rule::in(Booking::allowedStatuses())],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'integer', 'exists:drivers,id'],
            'requester_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'destination_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'preset' => ['nullable', Rule::in(['today', 'this_month'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
            'q.max' => 'Kata kunci pencarian maksimal 100 karakter.',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                if ($validator->errors()->has('from') || $validator->errors()->has('to')) {
                    return;
                }

                $from = $this->input('from');
                $to = $this->input('to');

                if (! is_string($from) || ! is_string($to) || $from === '' || $to === '') {
                    return;
                }

                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from);
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to);

                if ($fromDate->diffInDays($toDate) > 366) {
                    $validator->errors()->add('to', 'Date range cannot exceed 366 days.');
                }
            },
        ];
    }
}
