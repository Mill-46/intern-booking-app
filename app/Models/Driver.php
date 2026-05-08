<?php

namespace App\Models;

use Database\Factories\DriverFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'license_no', 'license_expiry', 'status'])]
class Driver extends Model
{
    /** @use HasFactory<DriverFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'license_expiry' => 'date',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VehicleUsage::class);
    }
}
