<div class="grid gap-4 md:grid-cols-2">
<input type="text" name="registration_no" value="{{ old('registration_no', $vehicle->registration_no ?? '') }}" placeholder="Nomor Polisi" class="field">
<select name="vehicle_type" class="field">
    <option value="person" @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') === 'person')>Angkutan Orang</option>
    <option value="cargo" @selected(old('vehicle_type', $vehicle->vehicle_type ?? '') === 'cargo')>Angkutan Barang</option>
</select>
<input type="text" name="brand" value="{{ old('brand', $vehicle->brand ?? '') }}" placeholder="Merek" class="field">
<input type="text" name="model" value="{{ old('model', $vehicle->model ?? '') }}" placeholder="Model" class="field">
<input type="number" step="0.01" name="fuel_capacity" value="{{ old('fuel_capacity', $vehicle->fuel_capacity ?? '') }}" placeholder="Kapasitas BBM (L)" class="field">
<input type="number" name="mileage" value="{{ old('mileage', $vehicle->mileage ?? 0) }}" placeholder="Kilometer Tempuh" class="field">
<select name="status" class="field">
    @foreach(['available','maintenance','rented'] as $status)
        <option value="{{ $status }}" @selected(old('status', $vehicle->status ?? '') === $status)>{{ $status }}</option>
    @endforeach
</select>
<select name="owner" class="field">
    <option value="company" @selected(old('owner', $vehicle->owner ?? '') === 'company')>Milik Perusahaan</option>
    <option value="rental" @selected(old('owner', $vehicle->owner ?? '') === 'rental')>Sewa</option>
</select>
</div>
