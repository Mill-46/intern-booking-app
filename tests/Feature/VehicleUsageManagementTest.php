<?php

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Site;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can manage vehicle usage records', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $l1 = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $l2 = User::factory()->create(['role' => User::ROLE_APPROVER_L2]);

    $vehicle = Vehicle::factory()->create();
    $driver = Driver::factory()->create(['status' => 'active']);
    $originSite = Site::factory()->create();
    $destinationSite = Site::factory()->create();

    $booking = Booking::factory()->create([
        'user_id' => $admin->id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'origin_site_id' => $originSite->id,
        'destination_site_id' => $destinationSite->id,
        'approver_l1_id' => $l1->id,
        'approver_l2_id' => $l2->id,
        'status' => Booking::STATUS_CONFIRMED,
    ]);

    $this->actingAs($admin)
        ->post(route('vehicle-usages.store'), [
            'booking_id' => $booking->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin_site_id' => $originSite->id,
            'destination_site_id' => $destinationSite->id,
            'started_at' => '2026-05-08 08:00:00',
            'ended_at' => '2026-05-08 12:00:00',
            'odometer_start' => 10500,
            'odometer_end' => 10620,
            'notes' => 'Perjalanan operasional site A',
        ])
        ->assertRedirect(route('vehicle-usages.index'));

    $usage = VehicleUsage::query()->firstOrFail();

    expect($usage->odometer_start)->toBe(10500)
        ->and($usage->odometer_end)->toBe(10620);

    $this->actingAs($admin)
        ->patch(route('vehicle-usages.update', $usage), [
            'booking_id' => $booking->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin_site_id' => $originSite->id,
            'destination_site_id' => $destinationSite->id,
            'started_at' => '2026-05-08 08:00:00',
            'ended_at' => '2026-05-08 13:00:00',
            'odometer_start' => 10500,
            'odometer_end' => 10650,
            'notes' => 'Update perjalanan',
        ])
        ->assertRedirect(route('vehicle-usages.index'));

    expect($usage->fresh()->odometer_end)->toBe(10650);

    $this->actingAs($admin)
        ->delete(route('vehicle-usages.destroy', $usage))
        ->assertRedirect(route('vehicle-usages.index'));

    $this->assertDatabaseMissing('vehicle_usages', ['id' => $usage->id]);
});
