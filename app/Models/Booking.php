<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'vehicle_id', 'driver_id', 'origin_site_id', 'destination_site_id', 'approver_l1_id', 'approver_l2_id', 'start_at', 'end_at', 'destination', 'purpose', 'status'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED_L1 = 'approved_l1';
    public const STATUS_APPROVED_L2 = 'approved_l2';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function approverL1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_l1_id');
    }

    public function approverL2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_l2_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function fuelConsumptions(): HasMany
    {
        return $this->hasMany(FuelConsumption::class);
    }

    public function vehicleUsages(): HasMany
    {
        return $this->hasMany(VehicleUsage::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Menunggu Approval L1',
            self::STATUS_APPROVED_L1 => 'Menunggu Approval L2',
            self::STATUS_APPROVED_L2 => 'Menunggu Konfirmasi Admin',
            self::STATUS_CONFIRMED => 'Siap Dieksekusi',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_REJECTED => 'Ditolak',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedStatuses(): array
    {
        return array_keys(self::statusLabels());
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }
}
