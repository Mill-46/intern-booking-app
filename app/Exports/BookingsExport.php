<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\VehicleUsage;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @param  Builder<Booking>  $query
     */
    public function __construct(private readonly Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Nomor Booking',
            'Tanggal Booking',
            'Admin Input',
            'Kendaraan',
            'Driver',
            'Site Asal',
            'Site Tujuan',
            'Tujuan Perjalanan',
            'Status',
            'Approver Level 1',
            'Approver Level 2',
            'Odometer Ringkasan',
        ];
    }

    /**
     * @param  Booking  $booking
     */
    public function map($booking): array
    {
        /** @var VehicleUsage|null $latestUsage */
        $latestUsage = $booking->vehicleUsages->sortByDesc('ended_at')->first();

        $odometerSummary = $latestUsage
            ? number_format($latestUsage->odometer_start) . ' - ' . number_format($latestUsage->odometer_end) . ' (' . number_format($latestUsage->distanceKm()) . ' km)'
            : '-';

        return [
            'BOOK-' . str_pad((string) $booking->id, 6, '0', STR_PAD_LEFT),
            $booking->start_at?->format('Y-m-d'),
            $booking->user->name,
            $booking->vehicle->registration_no,
            $booking->driver->name,
            $booking->originSite?->name ?? '-',
            $booking->destinationSite?->name ?? '-',
            $booking->destination,
            $booking->statusLabel(),
            $booking->approverL1?->name ?? '-',
            $booking->approverL2?->name ?? '-',
            $odometerSummary,
        ];
    }
}
