<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create update and delete users', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Approver User',
            'email' => 'approver-new@example.com',
            'role' => User::ROLE_APPROVER_L1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'approver-new@example.com')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('users.update', $user), [
            'name' => 'Approver Updated',
            'email' => 'approver-updated@example.com',
            'role' => User::ROLE_APPROVER_L2,
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->name)->toBe('Approver Updated')
        ->and($user->fresh()->role)->toBe(User::ROLE_APPROVER_L2);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

