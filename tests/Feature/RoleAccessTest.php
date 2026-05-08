<?php

use App\Models\Approval;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('approver cannot access admin routes', function () {
    $approver = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);

    $this->actingAs($approver)
        ->get(route('vehicles.index'))
        ->assertForbidden();

    $this->actingAs($approver)
        ->get(route('activity-logs.index'))
        ->assertForbidden();
});

test('admin cannot access approver routes', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('approvals.index'))
        ->assertForbidden();
});

test('approver can access own approval queue', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $approver = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);

    $booking = Booking::factory()->create(['user_id' => $admin->id]);

    Approval::factory()->create([
        'booking_id' => $booking->id,
        'approver_id' => $approver->id,
        'level' => 1,
        'status' => 'pending',
    ]);

    $this->actingAs($approver)
        ->get(route('approvals.index'))
        ->assertSuccessful();
});

test('approver cannot act on approval assigned to another approver', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $approverA = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);
    $approverB = User::factory()->create(['role' => User::ROLE_APPROVER_L1]);

    $booking = Booking::factory()->create(['user_id' => $admin->id]);

    $approval = Approval::factory()->create([
        'booking_id' => $booking->id,
        'approver_id' => $approverA->id,
        'level' => 1,
        'status' => 'pending',
    ]);

    $this->actingAs($approverB)
        ->post(route('approvals.approve', $approval))
        ->assertForbidden();
});
