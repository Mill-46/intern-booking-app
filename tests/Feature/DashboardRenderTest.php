<?php

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access dashboard and page contains operational insights', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    Booking::factory()->create([
        'user_id' => $admin->id,
        'status' => Booking::STATUS_SUBMITTED,
    ]);

    $response = $this->actingAs($admin)->get(route('dashboard'));
    $response->assertSuccessful();

    $response->assertSee('Distribusi Status Booking');
    $response->assertSee('Antrian Persetujuan');
    $response->assertSee('Top Kendaraan Terpakai');
    $response->assertSee('Top Site Tujuan');
    $response->assertSee('Tren Konsumsi BBM (6 Bulan)');
    $response->assertSee('Jadwal Servis Terdekat');
});
