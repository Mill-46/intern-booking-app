<div class="grid gap-4 md:grid-cols-2">
    <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" placeholder="Nama Pengemudi" class="field">
    <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" placeholder="No. Telepon" class="field">
    <input type="text" name="license_no" value="{{ old('license_no', $driver->license_no ?? '') }}" placeholder="Nomor SIM" class="field">
    <input type="date" name="license_expiry" value="{{ old('license_expiry', isset($driver) ? $driver->license_expiry?->format('Y-m-d') : '') }}" class="field">
    <select name="status" class="field">
        <option value="active" @selected(old('status', $driver->status ?? '') === 'active')>Aktif</option>
        <option value="inactive" @selected(old('status', $driver->status ?? '') === 'inactive')>Tidak Aktif</option>
    </select>
</div>