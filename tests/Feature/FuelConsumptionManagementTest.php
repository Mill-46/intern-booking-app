<?php

use App\Models\Booking;
use App\Models\FuelConsumption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create fuel consumption record', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $booking = Booking::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->post(route('fuel-consumptions.store'), [
            'booking_id' => $booking->id,
            'vehicle_id' => $booking->vehicle_id,
            'fuel_used' => 27.5,
            'recorded_at' => now()->format('Y-m-d H:i:s'),
        ])
        ->assertRedirect(route('fuel-consumptions.index'));

    $record = FuelConsumption::query()->first();

    expect($record)->not()->toBeNull()
        ->and((float) $record->fuel_used)->toBe(27.5);
});

test('approver cannot access fuel management pages', function () {
    $approver = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);

    $this->actingAs($approver)
        ->get(route('fuel-consumptions.index'))
        ->assertForbidden();
});

test('admin can view fuel consumption list', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $booking = Booking::factory()->create(['user_id' => $admin->id]);
    FuelConsumption::factory()->create([
        'booking_id' => $booking->id,
        'vehicle_id' => $booking->vehicle_id,
    ]);

    $this->actingAs($admin)
        ->get(route('fuel-consumptions.index'))
        ->assertSuccessful()
        ->assertSee('Catatan BBM');
});
