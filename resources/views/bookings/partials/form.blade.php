@props([
    'booking' => null,
    'vehicles',
    'drivers',
    'sites',
    'approversL1',
    'approversL2',
])

@php
    /** @var \App\Models\Booking|null $booking */
    $b = $booking;

    $vehicleId = old('vehicle_id', $b?->vehicle_id);
    $driverId = old('driver_id', $b?->driver_id);
    $originSiteId = old('origin_site_id', $b?->origin_site_id);
    $destinationSiteId = old('destination_site_id', $b?->destination_site_id);
    $approverL1Id = old('approver_l1_id', $b?->approver_l1_id);
    $approverL2Id = old('approver_l2_id', $b?->approver_l2_id);

    $startAtValue = old('start_at', $b?->start_at?->format('Y-m-d\\TH:i'));
    $endAtValue = old('end_at', $b?->end_at?->format('Y-m-d\\TH:i'));

    $destination = old('destination', $b?->destination);
    $purpose = old('purpose', $b?->purpose);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <select name="vehicle_id" class="field">
        <option value="">Pilih kendaraan</option>
        @foreach($vehicles as $vehicle)
            <option value="{{ $vehicle->id }}" @selected((string) $vehicleId === (string) $vehicle->id)>
                {{ $vehicle->registration_no }} - {{ $vehicle->brand }}
            </option>
        @endforeach
    </select>

    <select name="driver_id" class="field">
        <option value="">Pilih pengemudi</option>
        @foreach($drivers as $driver)
            <option value="{{ $driver->id }}" @selected((string) $driverId === (string) $driver->id)>
                {{ $driver->name }}
            </option>
        @endforeach
    </select>

    <select name="origin_site_id" class="field">
        <option value="">Pilih site asal</option>
        @foreach($sites as $site)
            <option value="{{ $site->id }}" @selected((string) $originSiteId === (string) $site->id)>
                {{ $site->name }} ({{ $site->site_type }})
            </option>
        @endforeach
    </select>

    <select name="destination_site_id" class="field">
        <option value="">Pilih site tujuan</option>
        @foreach($sites as $site)
            <option value="{{ $site->id }}" @selected((string) $destinationSiteId === (string) $site->id)>
                {{ $site->name }} ({{ $site->site_type }})
            </option>
        @endforeach
    </select>

    <select name="approver_l1_id" class="field">
        <option value="">Pilih Approver L1</option>
        @foreach($approversL1 as $approver)
            <option value="{{ $approver->id }}" @selected((string) $approverL1Id === (string) $approver->id)>
                {{ $approver->name }}
            </option>
        @endforeach
    </select>

    <select name="approver_l2_id" class="field">
        <option value="">Pilih Approver L2</option>
        @foreach($approversL2 as $approver)
            <option value="{{ $approver->id }}" @selected((string) $approverL2Id === (string) $approver->id)>
                {{ $approver->name }}
            </option>
        @endforeach
    </select>

    <input type="datetime-local" name="start_at" value="{{ $startAtValue }}" class="field">
    <input type="datetime-local" name="end_at" value="{{ $endAtValue }}" class="field">
</div>

<input type="text" name="destination" value="{{ $destination }}" placeholder="Detail tujuan" class="field">
<textarea name="purpose" placeholder="Tujuan pemakaian" class="field min-h-28">{{ $purpose }}</textarea>
