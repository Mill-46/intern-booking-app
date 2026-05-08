<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(): View
    {
        $filters = request()->only(['q']);

        $drivers = Driver::query()
            ->when(trim((string) ($filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $keyword = '%' . trim((string) $filters['q']) . '%';

                $query->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', $keyword)
                        ->orWhere('phone', 'like', $keyword)
                        ->orWhere('license_no', 'like', $keyword);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('drivers.index', compact('drivers', 'filters'));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $driver = Driver::create($request->validated());

        $this->logActivity($request, 'create_driver', 'Created driver #' . $driver->id, null, [
            'driver_id' => $driver->id,
        ]);

        return redirect()->route('drivers.index')->with('status', 'Driver created');
    }

    public function show(Driver $driver): View
    {
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $driver->update($request->validated());

        $this->logActivity($request, 'update_driver', 'Updated driver #' . $driver->id, null, [
            'driver_id' => $driver->id,
        ]);

        return redirect()->route('drivers.index')->with('status', 'Driver updated');
    }

    public function destroy(Request $request, Driver $driver): RedirectResponse
    {
        $driver->delete();

        $this->logActivity($request, 'delete_driver', 'Deleted driver #' . $driver->id, null, [
            'driver_id' => $driver->id,
        ]);

        return redirect()->route('drivers.index')->with('status', 'Driver deleted');
    }
}
