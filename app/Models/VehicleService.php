<?php

namespace App\Models;

use Database\Factories\VehicleServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['vehicle_id', 'service_date', 'service_type', 'workshop_name', 'cost', 'status', 'notes'])]
class VehicleService extends Model
{
    /** @use HasFactory<VehicleServiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
