<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->status === Booking::STATUS_DRAFT;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->status === Booking::STATUS_DRAFT;
    }

    public function submit(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->status === Booking::STATUS_DRAFT;
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->status === Booking::STATUS_APPROVED_L2;
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->status === Booking::STATUS_CONFIRMED;
    }
}
