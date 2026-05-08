@php
/** @var \App\Models\VehicleUsage $vehicleUsage */
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Pemesanan</label>
        <select name="booking_id" class="field" required>
            <option value="">Pilih pemesanan</option>
            @foreach($bookings as $booking)
            <option value="{{ $booking->id }}" @selected(old('booking_id', $vehicleUsage->booking_id) == $booking->id)>
                #{{ $booking->id }} - {{ $booking->vehicle->registration_no }} - {{ $booking->user->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Kendaraan</label>
        <select name="vehicle_id" class="field" required>
            <option value="">Pilih kendaraan</option>
            @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $vehicleUsage->vehicle_id) == $vehicle->id)>
                {{ $vehicle->registration_no }} - {{ $vehicle->brand }} {{ $vehicle->model }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Driver</label>
        <select name="driver_id" class="field" required>
            <option value="">Pilih driver</option>
            @foreach($drivers as $driver)
            <option value="{{ $driver->id }}" @selected(old('driver_id', $vehicleUsage->driver_id) == $driver->id)>
                {{ $driver->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Site Asal</label>
        <select name="origin_site_id" class="field">
            <option value="">-</option>
            @foreach($sites as $site)
            <option value="{{ $site->id }}" @selected(old('origin_site_id', $vehicleUsage->origin_site_id) == $site->id)>
                {{ $site->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Site Tujuan</label>
        <select name="destination_site_id" class="field">
            <option value="">-</option>
            @foreach($sites as $site)
            <option value="{{ $site->id }}" @selected(old('destination_site_id', $vehicleUsage->destination_site_id) == $site->id)>
                {{ $site->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Mulai</label>
        <input
            type="datetime-local"
            name="started_at"
            value="{{ old('started_at', optional($vehicleUsage->started_at)->format('Y-m-d\\TH:i')) }}"
            class="field"
            required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Selesai</label>
        <input
            type="datetime-local"
            name="ended_at"
            value="{{ old('ended_at', optional($vehicleUsage->ended_at)->format('Y-m-d\\TH:i')) }}"
            class="field"
            required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Odometer Awal (km)</label>
        <input
            type="number"
            min="0"
            name="odometer_start"
            value="{{ old('odometer_start', $vehicleUsage->odometer_start) }}"
            class="field"
            required>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Odometer Akhir (km)</label>
        <input
            type="number"
            min="0"
            name="odometer_end"
            value="{{ old('odometer_end', $vehicleUsage->odometer_end) }}"
            class="field"
            required>
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-semibold text-slate-700">Catatan</label>
        <textarea name="notes" rows="4" class="field">{{ old('notes', $vehicleUsage->notes) }}</textarea>
    </div>
</div>

<div class="mt-4 flex flex-wrap items-center justify-end gap-2">
    <button class="btn-primary" type="submit">Simpan</button>
    <a href="{{ route('vehicle-usages.index') }}" class="btn-soft">Batal</a>
</div>