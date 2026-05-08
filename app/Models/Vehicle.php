<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['registration_no', 'vehicle_type', 'brand', 'model', 'fuel_capacity', 'mileage', 'status', 'owner'])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function fuelConsumptions(): HasMany
    {
        return $this->hasMany(FuelConsumption::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(VehicleService::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VehicleUsage::class);
    }
}
