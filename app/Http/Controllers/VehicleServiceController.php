<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleServiceRequest;
use App\Http\Requests\UpdateVehicleServiceRequest;
use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleServiceController extends Controller
{
    public function index(): View
    {
        $filters = request()->only(['q', 'status']);

        $vehicleServices = VehicleService::with('vehicle')
            ->when(trim((string) ($filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $keyword = '%' . trim((string) $filters['q']) . '%';

                $query->where(function ($inner) use ($keyword) {
                    $inner->whereHas('vehicle', fn($q) => $q->where('registration_no', 'like', $keyword))
                        ->orWhere('service_type', 'like', $keyword)
                        ->orWhere('workshop_name', 'like', $keyword);
                });
            })
            ->when(trim((string) ($filters['status'] ?? '')) !== '', fn($query) => $query->where('status', $filters['status']))
            ->orderBy('service_date')
            ->paginate(15)
            ->withQueryString();

        return view('vehicle-services.index', compact('vehicleServices', 'filters'));
    }

    public function create(): View
    {
        return view('vehicle-services.create', [
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
        ]);
    }

    public function store(StoreVehicleServiceRequest $request): RedirectResponse
    {
        $vehicleService = VehicleService::create($request->validated());

        $this->logActivity($request, 'create_vehicle_service', 'Membuat jadwal servis #' . $vehicleService->id, null, [
            'vehicle_service_id' => $vehicleService->id,
            'vehicle_id' => $vehicleService->vehicle_id,
        ]);

        return redirect()->route('vehicle-services.index')->with('status', 'Jadwal servis berhasil dibuat.');
    }

    public function show(VehicleService $vehicleService): View
    {
        $vehicleService->load('vehicle');

        return view('vehicle-services.show', compact('vehicleService'));
    }

    public function edit(VehicleService $vehicleService): View
    {
        return view('vehicle-services.edit', [
            'vehicleService' => $vehicleService,
            'vehicles' => Vehicle::orderBy('registration_no')->get(),
        ]);
    }

    public function update(UpdateVehicleServiceRequest $request, VehicleService $vehicleService): RedirectResponse
    {
        $vehicleService->update($request->validated());

        $this->logActivity($request, 'update_vehicle_service', 'Memperbarui jadwal servis #' . $vehicleService->id, null, [
            'vehicle_service_id' => $vehicleService->id,
            'vehicle_id' => $vehicleService->vehicle_id,
        ]);

        return redirect()->route('vehicle-services.index')->with('status', 'Jadwal servis berhasil diperbarui.');
    }

    public function destroy(Request $request, VehicleService $vehicleService): RedirectResponse
    {
        $vehicleService->delete();

        $this->logActivity($request, 'delete_vehicle_service', 'Menghapus jadwal servis #' . $vehicleService->id, null, [
            'vehicle_service_id' => $vehicleService->id,
            'vehicle_id' => $vehicleService->vehicle_id,
        ]);

        return redirect()->route('vehicle-services.index')->with('status', 'Jadwal servis berhasil dihapus.');
    }
}
