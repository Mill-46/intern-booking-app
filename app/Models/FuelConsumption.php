<?php

namespace App\Models;

use Database\Factories\FuelConsumptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['booking_id', 'vehicle_id', 'fuel_used', 'recorded_at'])]
class FuelConsumption extends Model
{
    /** @use HasFactory<FuelConsumptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'fuel_used' => 'decimal:2',
            'recorded_at' => 'datetime',
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
}
