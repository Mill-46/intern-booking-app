<?php

namespace App\Http\Controllers;

use App\Exports\BookingsExport;
use App\Http\Requests\BookingReportFilterRequest;
use App\Models\Booking;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private const MAX_EXPORT_ROWS = 5000;

    public function bookings(BookingReportFilterRequest $request): BinaryFileResponse
    {
        $validated = $request->validated();
        $from = (string) ($validated['from'] ?? '');
        $to = (string) ($validated['to'] ?? '');
        $status = (string) ($validated['status'] ?? '');
        $vehicleId = (int) ($validated['vehicle_id'] ?? 0);
        $driverId = (int) ($validated['driver_id'] ?? 0);
        $requesterId = (int) ($validated['requester_id'] ?? 0);
        $originSiteId = (int) ($validated['origin_site_id'] ?? 0);
        $destinationSiteId = (int) ($validated['destination_site_id'] ?? 0);
        $keyword = trim((string) ($validated['q'] ?? ''));

        $query = Booking::with([
            'user',
            'vehicle',
            'driver',
            'originSite',
            'destinationSite',
            'approverL1',
            'approverL2',
            'vehicleUsages',
        ])->orderByDesc('id');

        if ($from !== '') {
            $query->whereDate('start_at', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('end_at', '<=', $to);
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($vehicleId > 0) {
            $query->where('vehicle_id', $vehicleId);
        }

        if ($driverId > 0) {
            $query->where('driver_id', $driverId);
        }

        if ($requesterId > 0) {
            $query->where('user_id', $requesterId);
        }

        if ($originSiteId > 0) {
            $query->where('origin_site_id', $originSiteId);
        }

        if ($destinationSiteId > 0) {
            $query->where('destination_site_id', $destinationSiteId);
        }

        if ($keyword !== '') {
            $query->where(function ($nestedQuery) use ($keyword): void {
                $nestedQuery->where('destination', 'like', '%' . $keyword . '%')
                    ->orWhere('purpose', 'like', '%' . $keyword . '%')
                    ->orWhereHas('vehicle', function ($vehicleQuery) use ($keyword): void {
                        $vehicleQuery->where('registration_no', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('driver', function ($driverQuery) use ($keyword): void {
                        $driverQuery->where('name', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($keyword): void {
                        $userQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        $totalRows = (clone $query)->count();
        if ($totalRows > self::MAX_EXPORT_ROWS) {
            throw ValidationException::withMessages([
                'export' => 'Export dibatasi maksimal ' . self::MAX_EXPORT_ROWS . ' baris. Persempit filter periode atau status.',
            ]);
        }

        $filename = 'bookings-' . now()->format('Ymd-His') . '.xlsx';

        $this->logActivity($request, 'export_bookings', 'Exported booking report to Excel', null, [
            'from' => $from !== '' ? $from : null,
            'to' => $to !== '' ? $to : null,
            'status' => $status !== '' ? $status : null,
            'vehicle_id' => $vehicleId > 0 ? $vehicleId : null,
            'driver_id' => $driverId > 0 ? $driverId : null,
            'requester_id' => $requesterId > 0 ? $requesterId : null,
            'destination_site_id' => $destinationSiteId > 0 ? $destinationSiteId : null,
            'q' => $keyword !== '' ? $keyword : null,
            'rows' => $totalRows,
        ]);

        return Excel::download(new BookingsExport($query), $filename);
    }
}
