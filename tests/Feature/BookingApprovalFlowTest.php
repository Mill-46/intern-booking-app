<?php

use App\Models\Approval;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('admin can submit booking and creates two approval levels', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_DRAFT,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.submit', $booking))
        ->assertRedirect();

    $booking->refresh();

    expect($booking->status)->toBe(Booking::STATUS_SUBMITTED)
        ->and(Approval::where('booking_id', $booking->id)->count())->toBe(2)
        ->and(Approval::where('booking_id', $booking->id)->where('approver_id', $l1->id)->where('level', 1)->exists())->toBeTrue()
        ->and(Approval::where('booking_id', $booking->id)->where('approver_id', $l2->id)->where('level', 2)->exists())->toBeTrue();

    Notification::assertSentTo([$l1, $l2], BookingStatusUpdatedNotification::class);
});

test('level 1 then level 2 approval moves booking to approved_l2', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_DRAFT,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
    ]);

    $this->actingAs($admin)->post(route('bookings.submit', $booking));

    $approvalL1 = Approval::where('booking_id', $booking->id)->where('level', 1)->firstOrFail();
    $approvalL2 = Approval::where('booking_id', $booking->id)->where('level', 2)->firstOrFail();

    $this->actingAs($l1)->post(route('approvals.approve', $approvalL1));
    expect($booking->fresh()->status)->toBe(Booking::STATUS_APPROVED_L1);

    $this->actingAs($l2)->post(route('approvals.approve', $approvalL2));
    expect($booking->fresh()->status)->toBe(Booking::STATUS_APPROVED_L2);
});

test('rejection at level 1 marks booking as rejected', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_DRAFT,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
    ]);

    $this->actingAs($admin)->post(route('bookings.submit', $booking));

    $approvalL1 = Approval::where('booking_id', $booking->id)->where('level', 1)->firstOrFail();

    $this->actingAs($l1)->post(route('approvals.reject', $approvalL1), [
        'comment' => 'Insufficient details',
    ]);

    expect($booking->fresh()->status)->toBe(Booking::STATUS_REJECTED)
        ->and($approvalL1->fresh()->status)->toBe('rejected');
});

test('booking creation is blocked when vehicle timeslot overlaps', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $vehicle = Vehicle::factory()->create();
    $driverA = Driver::factory()->create();
    $driverB = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    Booking::factory()->create([
        'user_id' => $admin->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driverA->id,
        'start_at' => '2026-05-10 09:00:00',
        'end_at' => '2026-05-10 12:00:00',
        'status' => Booking::STATUS_SUBMITTED,
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.store'), [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driverB->id,
            'origin_site_id' => $originSite->id,
            'destination_site_id' => $destinationSite->id,
            'approver_l1_id' => User::where('role', User::ROLE_APPROVER_L1)->firstOrFail()->id,
            'approver_l2_id' => User::where('role', User::ROLE_APPROVER_L2)->firstOrFail()->id,
            'start_at' => '2026-05-10 10:00:00',
            'end_at' => '2026-05-10 11:00:00',
            'destination' => 'Site A',
            'purpose' => 'Inspection',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('booking update is blocked when vehicle timeslot overlaps confirmed booking', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $vehicle = Vehicle::factory()->create();
    $driverA = Driver::factory()->create();
    $driverB = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    Booking::factory()->create([
        'user_id' => $admin->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driverA->id,
        'start_at' => '2026-05-10 09:00:00',
        'end_at' => '2026-05-10 12:00:00',
        'status' => Booking::STATUS_CONFIRMED,
    ]);

    $draftBooking = Booking::factory()->create([
        'user_id' => $admin->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driverB->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'start_at' => '2026-05-11 10:00:00',
        'end_at' => '2026-05-11 12:00:00',
        'status' => Booking::STATUS_DRAFT,
    ]);

    $this->actingAs($admin)
        ->put(route('bookings.update', $draftBooking), [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driverB->id,
            'origin_site_id' => $originSite->id,
            'destination_site_id' => $destinationSite->id,
            'approver_l1_id' => $l1->id,
            'approver_l2_id' => $l2->id,
            'start_at' => '2026-05-10 10:00:00',
            'end_at' => '2026-05-10 11:00:00',
            'destination' => 'Site A',
            'purpose' => 'Inspection',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('level 2 approver cannot reject booking before level 1 approval', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_DRAFT,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
    ]);

    $this->actingAs($admin)->post(route('bookings.submit', $booking));

    $approvalL2 = Approval::where('booking_id', $booking->id)->where('level', 2)->firstOrFail();

    $this->actingAs($l2)
        ->post(route('approvals.reject', $approvalL2), ['comment' => 'No'])
        ->assertStatus(422);
});

test('bookings export endpoint returns xlsx download', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    Booking::factory()->create(['user_id' => $admin->id]);

    $response = $this->actingAs($admin)->get(route('exports.bookings'));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('admin can confirm and complete booking after level 2 approval', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_APPROVED_L2,
    ]);

    $this->actingAs($admin)
        ->post(route('bookings.confirm', $booking))
        ->assertRedirect();

    expect($booking->fresh()->status)->toBe(Booking::STATUS_CONFIRMED);

    $this->actingAs($admin)
        ->post(route('bookings.complete', $booking))
        ->assertRedirect();

    expect($booking->fresh()->status)->toBe(Booking::STATUS_COMPLETED);
});

test('booking payload is sanitized before persisting', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    $this->actingAs($admin)
        ->post(route('bookings.store'), [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin_site_id' => $originSite->id,
            'destination_site_id' => $destinationSite->id,
            'approver_l1_id' => $l1->id,
            'approver_l2_id' => $l2->id,
            'start_at' => '2026-05-12 10:00:00',
            'end_at' => '2026-05-12 11:00:00',
            'destination' => '<script>alert(1)</script> Site A',
            'purpose' => 'Need <b>urgent</b> inspection',
        ])
        ->assertRedirect();

    $booking = Booking::query()->latest('id')->firstOrFail();

    expect($booking->destination)->not->toContain('<script>')
        ->and($booking->purpose)->not->toContain('<b>');
});

test('approval comment is sanitized before persisting', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_DRAFT,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
    ]);

    $this->actingAs($admin)->post(route('bookings.submit', $booking));

    $approvalL1 = Approval::where('booking_id', $booking->id)->where('level', 1)->firstOrFail();

    $this->actingAs($l1)
        ->post(route('approvals.approve', $approvalL1), [
            'comment' => '<script>alert(1)</script>approved',
        ])
        ->assertRedirect();

    expect($approvalL1->fresh()->comment)->not->toContain('<script>');
});
