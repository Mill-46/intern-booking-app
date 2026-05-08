@php
    $selectedBookingId = old('booking_id', $fuelConsumption->booking_id ?? '');
    $selectedVehicleId = old('vehicle_id', $fuelConsumption->vehicle_id ?? '');
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium">Pemesanan</label>
        <select name="booking_id" class="field" required>
            <option value="">Pilih pemesanan</option>
            @foreach($bookings as $booking)
                <option value="{{ $booking->id }}" @selected((string) $selectedBookingId === (string) $booking->id)>
                    #{{ $booking->id }} - {{ $booking->user->name }} - {{ $booking->vehicle->registration_no }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Kendaraan</label>
        <select name="vehicle_id" class="field" required>
            <option value="">Pilih kendaraan</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected((string) $selectedVehicleId === (string) $vehicle->id)>
                    {{ $vehicle->registration_no }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">BBM Terpakai (liter)</label>
        <input type="number" name="fuel_used" step="0.01" min="0.01" class="field" value="{{ old('fuel_used', $fuelConsumption->fuel_used ?? '') }}" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Waktu Pencatatan</label>
        <input type="datetime-local" name="recorded_at" class="field" value="{{ old('recorded_at', isset($fuelConsumption) && $fuelConsumption->recorded_at ? $fuelConsumption->recorded_at->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i')) }}" required>
    </div>
</div>
