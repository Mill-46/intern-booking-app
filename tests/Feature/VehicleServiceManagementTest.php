<?php

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create vehicle service schedule', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $vehicle = Vehicle::factory()->create();

    $this->actingAs($admin)
        ->post(route('vehicle-services.store'), [
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-06-01',
            'service_type' => 'Periodic Service',
            'workshop_name' => 'Main Workshop',
            'cost' => 500000,
            'status' => 'scheduled',
            'notes' => 'Quarterly service plan',
        ])
        ->assertRedirect(route('vehicle-services.index'));

    expect(VehicleService::query()->count())->toBe(1)
        ->and(ActivityLog::query()->where('action', 'create_vehicle_service')->exists())->toBeTrue();
});

test('approver cannot access vehicle service management pages', function () {
    $approver = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);

    $this->actingAs($approver)
        ->get(route('vehicle-services.index'))
        ->assertForbidden();
});

test('admin can update and delete vehicle service', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $service = VehicleService::factory()->create();

    $this->actingAs($admin)
        ->put(route('vehicle-services.update', $service), [
            'vehicle_id' => $service->vehicle_id,
            'service_date' => $service->service_date->format('Y-m-d'),
            'service_type' => 'Oil Change',
            'workshop_name' => 'Partner Workshop',
            'cost' => 300000,
            'status' => 'completed',
            'notes' => 'Done',
        ])
        ->assertRedirect(route('vehicle-services.index'));

    expect($service->fresh()->status)->toBe('completed');

    $this->actingAs($admin)
        ->delete(route('vehicle-services.destroy', $service))
        ->assertRedirect(route('vehicle-services.index'));

    expect(VehicleService::query()->count())->toBe(0)
        ->and(ActivityLog::query()->where('action', 'delete_vehicle_service')->exists())->toBeTrue();
});
