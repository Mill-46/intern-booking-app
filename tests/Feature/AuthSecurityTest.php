<?php

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login attempts are rate limited after multiple invalid tries', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    foreach (range(1, 6) as $attempt) {
        $this->post(route('login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    $this->post(route('login.attempt'), [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});

test('failed login attempt is recorded in activity logs', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $this->post(route('login.attempt'), [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $log = ActivityLog::query()->where('action', 'login_failed')->first();

    expect($log)->not->toBeNull()
        ->and($log?->description)->toBe('Percobaan login gagal')
        ->and(data_get($log?->metadata, 'email_hash'))->not->toBeEmpty();
});

test('login page generates secure asset and form urls behind a proxy', function () {
    $this->withServerVariables([
        'HTTP_HOST' => 'intern-booking-app-production.up.railway.app',
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_HOST' => 'intern-booking-app-production.up.railway.app',
    ])->get(route('login'))
        ->assertOk()
        ->assertSee('https://intern-booking-app-production.up.railway.app/build/assets/', false)
        ->assertSee('action="https://intern-booking-app-production.up.railway.app/login"', false);
});
