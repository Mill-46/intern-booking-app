<?php

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can filter bookings by status and requester', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $requesterA = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $requesterB = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    $match = Booking::factory()->create([
        'user_id' => $requesterA->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'status' => Booking::STATUS_SUBMITTED,
    ]);

    $other = Booking::factory()->create([
        'user_id' => $requesterB->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'status' => Booking::STATUS_DRAFT,
    ]);

    $response = $this->actingAs($admin)->get(route('bookings.index', [
        'status' => Booking::STATUS_SUBMITTED,
        'requester_id' => $requesterA->id,
    ]));

    $response->assertSuccessful();
    $response->assertSee('BK-' . str_pad($match->id, 5, '0', STR_PAD_LEFT), false);
    $response->assertDontSee('BK-' . str_pad($other->id, 5, '0', STR_PAD_LEFT), false);
});

test('booking index rejects invalid filter payload', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('bookings.index', [
            'status' => 'invalid-status',
            'from' => '2026-99-10',
        ]))
        ->assertRedirect()
        ->assertSessionHasErrors(['status', 'from']);
});

test('booking export rejects invalid filter payload', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $site = Site::factory()->create();
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();

    Booking::factory()->create([
        'user_id' => $admin->id,
        'destination_site_id' => $site->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
    ]);

    $this->actingAs($admin)
        ->get(route('exports.bookings', [
            'from' => 'not-a-date',
            'status' => 'anything',
        ]))
        ->assertRedirect()
        ->assertSessionHasErrors(['from', 'status']);
});

test('booking export rejects overly long date range', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('exports.bookings', [
            'from' => '2024-01-01',
            'to' => '2026-01-10',
        ]))
        ->assertRedirect()
        ->assertSessionHasErrors(['to']);
});

test('booking export rejects payload when row count exceeds limit', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);
    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create();
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    Booking::factory()->count(5001)->create([
        'user_id' => $admin->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'status' => Booking::STATUS_COMPLETED,
        'start_at' => '2026-05-01 08:00:00',
        'end_at' => '2026-05-01 10:00:00',
    ]);

    $this->actingAs($admin)
        ->get(route('exports.bookings', [
            'from' => '2026-05-01',
            'to' => '2026-05-31',
            'status' => Booking::STATUS_COMPLETED,
        ]))
        ->assertRedirect()
        ->assertSessionHasErrors(['export']);
});
