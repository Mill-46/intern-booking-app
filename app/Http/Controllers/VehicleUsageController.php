<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleUsageRequest;
use App\Http\Requests\UpdateVehicleUsageRequest;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\Vehicle;
use App\Models\VehicleUsage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleUsageController extends Controller
{
    private const BOOKING_PICKER_LIMIT = 300;

    public function index(): View
    {
        $filters = request()->only(['q']);

        $vehicleUsages = VehicleUsage::with(['booking.user', 'vehicle', 'driver', 'originSite', 'destinationSite'])
            ->when(trim((string) ($filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $keyword = '%' . trim((string) $filters['q']) . '%';

                $query->where(function ($inner) use ($keyword) {
                    $inner->whereHas('vehicle', fn($q) => $q->where('registration_no', 'like', $keyword))
                        ->orWhereHas('driver', fn($q) => $q->where('name', 'like', $keyword))
                        ->orWhereHas('booking', fn($q) => $q->where('destination', 'like', $keyword)
                            ->orWhere('id', 'like', $keyword))
                        ->orWhereHas('originSite', fn($q) => $q->where('name', 'like', $keyword))
                        ->orWhereHas('destinationSite', fn($q) => $q->where('name', 'like', $keyword));
                });
            })
            ->latest('started_at')
            ->paginate(15)
            ->withQueryString();

        return view('vehicle-usages.index', compact('vehicleUsages', 'filters'));
    }

    public function create(): View
    {
        return view('vehicle-usages.create', [
            'vehicleUsage' => new VehicleUsage(),
            'bookings' => Booking::with(['vehicle', 'user'])
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                ->latest()
                ->limit(self::BOOKING_PICKER_LIMIT)
                ->get(),
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
            'drivers' => Driver::where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
        ]);
    }

    public function store(StoreVehicleUsageRequest $request): RedirectResponse
    {
        $vehicleUsage = VehicleUsage::create($request->validated());

        $this->logActivity($request, 'record_vehicle_usage', 'Recorded vehicle usage #' . $vehicleUsage->id, null, [
            'vehicle_usage_id' => $vehicleUsage->id,
            'booking_id' => $vehicleUsage->booking_id,
            'vehicle_id' => $vehicleUsage->vehicle_id,
            'distance_km' => $vehicleUsage->distanceKm(),
        ]);

        return redirect()->route('vehicle-usages.index')->with('status', 'Riwayat pemakaian berhasil ditambahkan.');
    }

    public function show(VehicleUsage $vehicleUsage): View
    {
        $vehicleUsage->load(['booking.user', 'vehicle', 'driver', 'originSite', 'destinationSite']);

        return view('vehicle-usages.show', compact('vehicleUsage'));
    }

    public function edit(VehicleUsage $vehicleUsage): View
    {
        return view('vehicle-usages.edit', [
            'vehicleUsage' => $vehicleUsage,
            'bookings' => Booking::with(['vehicle', 'user'])
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                ->latest()
                ->limit(self::BOOKING_PICKER_LIMIT)
                ->get(),
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
            'drivers' => Driver::where('status', 'active')->orderBy('name')->get(),
            'sites' => Site::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateVehicleUsageRequest $request, VehicleUsage $vehicleUsage): RedirectResponse
    {
        $vehicleUsage->update($request->validated());

        $this->logActivity($request, 'update_vehicle_usage', 'Updated vehicle usage #' . $vehicleUsage->id, null, [
            'vehicle_usage_id' => $vehicleUsage->id,
            'booking_id' => $vehicleUsage->booking_id,
            'vehicle_id' => $vehicleUsage->vehicle_id,
            'distance_km' => $vehicleUsage->distanceKm(),
        ]);

        return redirect()->route('vehicle-usages.index')->with('status', 'Riwayat pemakaian berhasil diperbarui.');
    }

    public function destroy(Request $request, VehicleUsage $vehicleUsage): RedirectResponse
    {
        $vehicleUsageId = $vehicleUsage->id;
        $bookingId = $vehicleUsage->booking_id;
        $vehicleId = $vehicleUsage->vehicle_id;

        $vehicleUsage->delete();

        $this->logActivity($request, 'delete_vehicle_usage', 'Deleted vehicle usage #' . $vehicleUsageId, null, [
            'vehicle_usage_id' => $vehicleUsageId,
            'booking_id' => $bookingId,
            'vehicle_id' => $vehicleId,
        ]);

        return redirect()->route('vehicle-usages.index')->with('status', 'Riwayat pemakaian berhasil dihapus.');
    }
}
