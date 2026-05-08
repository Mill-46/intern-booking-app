<?php

namespace App\Models;

use Database\Factories\VehicleUsageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'booking_id',
    'vehicle_id',
    'driver_id',
    'origin_site_id',
    'destination_site_id',
    'started_at',
    'ended_at',
    'odometer_start',
    'odometer_end',
    'notes',
])]
class VehicleUsage extends Model
{
    /** @use HasFactory<VehicleUsageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function originSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'origin_site_id');
    }

    public function destinationSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'destination_site_id');
    }

    public function distanceKm(): int
    {
        return max(0, $this->odometer_end - $this->odometer_start);
    }
}
