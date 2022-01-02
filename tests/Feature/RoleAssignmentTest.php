<?php

use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;

beforeEach(function () {
    auth()->login(User::factory()->create());
    $this->merchant = Merchant::factory()->create();
});

test('user can be assigned a single role', function () {
    $role = Role::getRandomValue();

    auth()->user()->assign($role);

    expect(auth()->user()->roles())->toBeInstanceOf(RoleCollection::class);
    $this->assertCount(1, auth()->user()->roles()->toArray());

    expect(auth()->user()->roles()->toArray())->toContain($role);

    expect(
        auth()->user()->modelRoles()->whereRole($role)->exists()
    )->toBeTrue();
});

test('user can be assigned multiple roles', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->assign($firstRole, $secondRole);
// dd(auth()->user()->roles());
// dd(auth()->user()->roles()->toArray());

// foreach (auth()->user()->roles() as $role) {
//     dd($role);
// }
    expect(auth()->user()->roles())->toBeInstanceOf(RoleCollection::class);
    $this->assertCount(2, auth()->user()->roles()->toArray());

    expect(auth()->user()->roles()->toArray())->toContain($firstRole, $secondRole);

    expect(
        auth()->user()->modelRoles()->whereRole($firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->modelRoles()->whereRole($secondRole)->exists()
    )->toBeTrue();
});

test('user can be assigned multiple roles as an array', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->assign([$firstRole, $secondRole]);

    $this->assertCount(2, auth()->user()->roles()->toArray());

    expect(auth()->user()->roles()->toArray())->toContain($secondRole, $firstRole);

    expect(
        auth()->user()->modelRoles()->whereRole($firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->modelRoles()->whereRole($secondRole)->exists()
    )->toBeTrue();
});

test('pivot table can be assigned a role', function () {
    $role = Role::getRandomValue();

    auth()->user()->of($this->merchant)->assign($role);

    expect(auth()->user()->of($this->merchant)->roles())->toBeInstanceOf(RoleCollection::class);

    $this->assertCount(1, auth()->user()->of($this->merchant)->roles()->toArray());

    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($role);

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $role)->exists()
    )->toBeTrue();
});

test('pivot table can be assigned multiple roles', function () {
    $firstRole = Role::getRandomValue();
    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->of($this->merchant)->assign($firstRole, $secondRole);

    expect(auth()->user()->of($this->merchant)->roles())->toBeInstanceOf(RoleCollection::class);
    $this->assertCount(2, auth()->user()->of($this->merchant)->roles()->toArray());

    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($firstRole);
    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($secondRole);
    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($secondRole, $firstRole);

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $secondRole)->exists()
    )->toBeTrue();
});

test('pivot table can be assigned multiple roles as an array', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->of($this->merchant)->assign([$firstRole, $secondRole]);

    expect(auth()->user()->of($this->merchant)->roles())->toBeInstanceOf(RoleCollection::class);

    $this->assertCount(2, auth()->user()->of($this->merchant)->roles()->toArray());

    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($firstRole);
    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($secondRole);
    expect(auth()->user()->of($this->merchant)->roles()->toArray())->toContain($secondRole, $firstRole);

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $secondRole)->exists()
    )->toBeTrue();
});
