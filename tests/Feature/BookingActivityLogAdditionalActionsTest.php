<?php

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('reject_booking writes activity log (level 1 and level 2)', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    $this->actingAs($admin)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'start_at' => '2026-05-10 09:00:00',
        'end_at' => '2026-05-10 12:00:00',
        'destination' => $destinationSite->name,
        'purpose' => 'Inspection',
    ])->assertRedirect(route('bookings.index'));

    $booking = Booking::query()->latest('id')->firstOrFail();

    $this->actingAs($admin)->post(route('bookings.submit', $booking))->assertRedirect();

    $approvalL1 = Approval::query()->where('booking_id', $booking->id)->where('level', 1)->firstOrFail();

    $this->actingAs($l1)->post(route('approvals.reject', $approvalL1), ['comment' => 'Rejected'])
        ->assertRedirect();

    expect(ActivityLog::query()->where('action', 'reject_booking')->exists())->toBeTrue();
});

test('update_booking and delete_booking write activity logs', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $this->actingAs($admin)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'start_at' => '2026-05-10 09:00:00',
        'end_at' => '2026-05-10 12:00:00',
        'destination' => $destinationSite->name,
        'purpose' => 'Inspection',
    ])->assertRedirect(route('bookings.index'));

    $booking = Booking::query()->latest('id')->firstOrFail();

    // update while still draft
    $this->actingAs($admin)->put(route('bookings.update', $booking), [
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'start_at' => '2026-05-11 09:00:00',
        'end_at' => '2026-05-11 12:00:00',
        'destination' => $destinationSite->name,
        'purpose' => 'Updated purpose',
    ])->assertRedirect(route('bookings.index'));

    // delete
    $this->actingAs($admin)->delete(route('bookings.destroy', $booking))
        ->assertRedirect(route('bookings.index'));

    expect(ActivityLog::query()->where('action', 'update_booking')->exists())->toBeTrue();
    expect(ActivityLog::query()->where('action', 'delete_booking')->exists())->toBeTrue();
});
