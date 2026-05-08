<?php

namespace App\Models;

use Database\Factories\SiteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'site_type', 'region'])]
class Site extends Model
{
    /** @use HasFactory<SiteFactory> */
    use HasFactory;

    public const TYPE_HEAD_OFFICE = 'head_office';
    public const TYPE_BRANCH_OFFICE = 'branch_office';
    public const TYPE_MINE_SITE = 'mine_site';

    public function originBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'origin_site_id');
    }

    public function destinationBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'destination_site_id');
    }
}
