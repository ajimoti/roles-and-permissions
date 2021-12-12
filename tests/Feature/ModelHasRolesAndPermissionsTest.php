<?php

use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(fn () => User::factory()->create());

it('can test', function () {

// dd(User::create([
//     'name' => 'Ajimoti John',
//     'email' => 'ibukunajimoti@gmail.com',
//     'email_verified_at' => now(),
//     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
//     'remember_token' => \Illuminate\Support\Str::random(10),
    // ]));

    expect(true)->toBeTrue();
});
