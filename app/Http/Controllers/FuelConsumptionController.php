<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuelConsumptionRequest;
use App\Http\Requests\UpdateFuelConsumptionRequest;
use App\Models\Booking;
use App\Models\FuelConsumption;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FuelConsumptionController extends Controller
{
    private const BOOKING_PICKER_LIMIT = 300;

    public function index(): View
    {
        $filters = request()->only(['q']);

        $fuelConsumptions = FuelConsumption::with(['booking.user', 'vehicle'])
            ->when(trim((string) ($filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $keyword = '%' . trim((string) $filters['q']) . '%';

                $query->where(function ($inner) use ($keyword) {
                    $inner->whereHas('vehicle', fn($q) => $q->where('registration_no', 'like', $keyword))
                        ->orWhereHas('booking', fn($q) => $q->where('destination', 'like', $keyword)
                            ->orWhere('id', 'like', $keyword));
                });
            })
            ->latest('recorded_at')
            ->paginate(15)
            ->withQueryString();

        return view('fuel-consumptions.index', compact('fuelConsumptions', 'filters'));
    }

    public function create(): View
    {
        return view('fuel-consumptions.create', [
            'bookings' => Booking::with(['vehicle', 'user'])
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                ->latest()
                ->limit(self::BOOKING_PICKER_LIMIT)
                ->get(),
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
        ]);
    }

    public function store(StoreFuelConsumptionRequest $request): RedirectResponse
    {
        $fuelConsumption = FuelConsumption::create($request->validated());

        $this->logActivity($request, 'record_fuel', 'Mencatat konsumsi BBM #' . $fuelConsumption->id, null, [
            'fuel_consumption_id' => $fuelConsumption->id,
            'booking_id' => $fuelConsumption->booking_id,
            'vehicle_id' => $fuelConsumption->vehicle_id,
        ]);

        return redirect()->route('fuel-consumptions.index')->with('status', 'Catatan BBM berhasil ditambahkan.');
    }

    public function show(FuelConsumption $fuelConsumption): View
    {
        $fuelConsumption->load(['booking.user', 'vehicle']);

        return view('fuel-consumptions.show', compact('fuelConsumption'));
    }

    public function edit(FuelConsumption $fuelConsumption): View
    {
        return view('fuel-consumptions.edit', [
            'fuelConsumption' => $fuelConsumption,
            'bookings' => Booking::with(['vehicle', 'user'])
                ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
                ->latest()
                ->limit(self::BOOKING_PICKER_LIMIT)
                ->get(),
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
        ]);
    }

    public function update(UpdateFuelConsumptionRequest $request, FuelConsumption $fuelConsumption): RedirectResponse
    {
        $fuelConsumption->update($request->validated());

        $this->logActivity($request, 'update_fuel', 'Memperbarui konsumsi BBM #' . $fuelConsumption->id, null, [
            'fuel_consumption_id' => $fuelConsumption->id,
            'booking_id' => $fuelConsumption->booking_id,
            'vehicle_id' => $fuelConsumption->vehicle_id,
        ]);

        return redirect()->route('fuel-consumptions.index')->with('status', 'Catatan BBM berhasil diperbarui.');
    }

    public function destroy(Request $request, FuelConsumption $fuelConsumption): RedirectResponse
    {
        $fuelConsumption->delete();

        $this->logActivity($request, 'delete_fuel', 'Menghapus konsumsi BBM #' . $fuelConsumption->id, null, [
            'fuel_consumption_id' => $fuelConsumption->id,
            'booking_id' => $fuelConsumption->booking_id,
            'vehicle_id' => $fuelConsumption->vehicle_id,
        ]);

        return redirect()->route('fuel-consumptions.index')->with('status', 'Catatan BBM berhasil dihapus.');
    }
}
