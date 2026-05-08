@php
    $selectedVehicleId = old('vehicle_id', optional($vehicleService)->vehicle_id ?? '');
    $selectedStatus = old('status', optional($vehicleService)->status ?? 'scheduled');
@endphp

<div class="grid gap-3 md:grid-cols-2">
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
        <label class="mb-1 block text-sm font-medium">Tanggal Servis</label>
        <input type="date" name="service_date" value="{{ old('service_date', optional(optional($vehicleService)->service_date)->format('Y-m-d')) }}" class="field" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Jenis Servis</label>
        <input type="text" name="service_type" value="{{ old('service_type', optional($vehicleService)->service_type ?? '') }}" class="field" placeholder="Berkala / Ganti Oli / Perbaikan" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Nama Bengkel</label>
        <input type="text" name="workshop_name" value="{{ old('workshop_name', optional($vehicleService)->workshop_name ?? '') }}" class="field" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Biaya</label>
        <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', optional($vehicleService)->cost ?? '0') }}" class="field" required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium">Status</label>
        <select name="status" class="field" required>
            @foreach(['scheduled', 'in_progress', 'completed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="mb-1 mt-3 block text-sm font-medium">Catatan</label>
    <textarea name="notes" rows="3" class="field">{{ old('notes', optional($vehicleService)->notes ?? '') }}</textarea>
</div>
