<?php

use App\Models\Approval;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('booking workflow writes activity logs for each main action', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    // Create draft booking via admin
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

    // Submit booking
    $this->actingAs($admin)->post(route('bookings.submit', $booking))->assertRedirect();

    // Approve L1 then L2
    $approvalL1 = Approval::query()->where('booking_id', $booking->id)->where('level', 1)->firstOrFail();
    $approvalL2 = Approval::query()->where('booking_id', $booking->id)->where('level', 2)->firstOrFail();

    $this->actingAs($l1)->post(route('approvals.approve', $approvalL1))->assertRedirect();
    $this->actingAs($l2)->post(route('approvals.approve', $approvalL2))->assertRedirect();

    $booking->refresh();

    // Confirm then complete (admin)
    $this->actingAs($admin)->post(route('bookings.confirm', $booking))->assertRedirect();
    $this->actingAs($admin)->post(route('bookings.complete', $booking))->assertRedirect();

    // Assert activity log entries exist for each stage
    $actions = [
        'create_booking',
        'submit_booking',
        'approve_booking',
        'confirm_booking',
        'complete_booking',
    ];

    foreach ($actions as $action) {
        expect(ActivityLog::query()->where('action', $action)->exists())
            ->toBeTrue();
    }
});
