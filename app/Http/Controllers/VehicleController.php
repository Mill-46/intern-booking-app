<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        $filters = request()->only(['q']);

        $vehicles = Vehicle::query()
            ->when(trim((string) ($filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $keyword = '%' . trim((string) $filters['q']) . '%';

                $query->where(function ($inner) use ($keyword) {
                    $inner->where('registration_no', 'like', $keyword)
                        ->orWhere('brand', 'like', $keyword)
                        ->orWhere('model', 'like', $keyword)
                        ->orWhere('owner', 'like', $keyword)
                        ->orWhere('vehicle_type', 'like', $keyword);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles', 'filters'));
    }

    public function create(): View
    {
        return view('vehicles.create');
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($request->validated());

        $this->logActivity($request, 'create_vehicle', 'Created vehicle #' . $vehicle->id, null, [
            'vehicle_id' => $vehicle->id,
        ]);

        return redirect()->route('vehicles.index')->with('status', 'Vehicle created');
    }

    public function show(Vehicle $vehicle): View
    {
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        $this->logActivity($request, 'update_vehicle', 'Updated vehicle #' . $vehicle->id, null, [
            'vehicle_id' => $vehicle->id,
        ]);

        return redirect()->route('vehicles.index')->with('status', 'Vehicle updated');
    }

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        DB::transaction(function () use ($vehicle): void {
            // Some foreign keys are configured as "restrictOnDelete", so we must
            // delete dependent rows explicitly before deleting the vehicle.
            $vehicle->usages()->delete();
            $vehicle->services()->delete();
            $vehicle->fuelConsumptions()->delete();
            $vehicle->bookings()->delete();

            $vehicle->delete();
        });

        $this->logActivity($request, 'delete_vehicle', 'Deleted vehicle #' . $vehicle->id, null, [
            'vehicle_id' => $vehicle->id,
        ]);

        return redirect()->route('vehicles.index')->with('status', 'Vehicle deleted');
    }
}
