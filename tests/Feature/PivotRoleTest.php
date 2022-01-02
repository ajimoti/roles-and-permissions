<?php

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    auth()->login(User::factory()->create());
    $this->merchant = Merchant::factory()->create();
    $this->role = Role::getRandomValue();

    do {
        $this->secondRole = Role::getRandomValue();
    } while ($this->role == $this->secondRole);

    auth()->user()->of($this->merchant)->assign($this->role);
});

it('has role permissions', function () {
    expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($this->role)))->toBeTrue();
});

it('can access every permission that belongs to the given role', function () {
    foreach (Role::getPermissions($this->role) as $permission) {
        expect(auth()->user()->of($this->merchant)->can($permission))->toBeTrue();
    }
});

it('is not given other role permissions', function () {
    foreach (Role::all()->toArray() as $role) {
        if ($role != $this->role) {
            expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($role)))->toBeFalse();
        }
    }
});

it('expect the role authorization to be valid', function () {
    expect(auth()->user()->of($this->merchant)->authorizeRole($this->role))->toBeTrue();
});

it('expect role authorization for other roles to throw exception', function () {
    $otherRoles = [];
    foreach (Role::all()->toArray() as $role) {
        if ($role != $this->role) {
            $otherRoles[] = $role;
            expect(fn () => auth()->user()->of($this->merchant)->authorizeRole($role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => auth()->user()->of($this->merchant)->authorizeRole($otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
    expect(fn () => auth()->user()->of($this->merchant)->authorizeRole(...$otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can be assigned new roles', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    expect(auth()->user()->of($this->merchant)->hasRole($this->secondRole))->toBeTrue();
});

it('can perform permissions of new roles and previous roles', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    foreach (Role::getPermissions($this->secondRole) as $permission) {
        expect(auth()->user()->of($this->merchant)->can($permission))->toBeTrue();
    }

    foreach (Role::getPermissions($this->role) as $permission) {
        expect(auth()->user()->of($this->merchant)->can($permission))->toBeTrue();
    }
});

it('has permissions of new roles and older role', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($this->role)))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($this->secondRole)))->toBeTrue();

    expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($this->role, $this->secondRole)))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->has(Role::getPermissions($this->secondRole, $this->role)))->toBeTrue();
});

it('expect role authorization for multiple roles are valid', function () {
    expect(auth()->user()->of($this->merchant)->authorizeRole($this->role, $this->role))->toBeTrue();
});

it('expect role authorization for other unassigned roles to throw exception', function () {
    $otherRoles = [];
    foreach (Role::all()->toArray() as $role) {
        if ($role !== $this->role) {
            $otherRoles[] = $role;
            expect(fn () => auth()->user()->of($this->merchant)->authorizeRole($role, $this->role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => auth()->user()->of($this->merchant)->authorizeRole($otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can remove specific role', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    auth()->user()->of($this->merchant)->removeRoles($this->secondRole);

    expect(auth()->user()->of($this->merchant)->hasRole($this->secondRole))->toBeFalse();
});

it('can remove multiple roles', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    auth()->user()->of($this->merchant)->removeRoles($this->secondRole, $this->role);

    expect(auth()->user()->of($this->merchant)->hasRole($this->secondRole, $this->role))->toBeFalse();
    expect(auth()->user()->of($this->merchant)->hasRole($this->role))->toBeFalse();
    expect(auth()->user()->of($this->merchant)->hasRole($this->secondRole))->toBeFalse();
});

it('can remove all roles', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    auth()->user()->of($this->merchant)->removeRoles();

    expect(auth()->user()->of($this->merchant)->hasRole($this->role))->toBeFalse();
    expect(auth()->user()->of($this->merchant)->hasRole($this->secondRole))->toBeFalse();
    expect(auth()->user()->of($this->merchant)->hasRole($this->role, $this->secondRole))->toBeFalse();
    expect(fn () => auth()->user()->of($this->merchant)->authorizeRole($this->role, $this->secondRole))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('record still exists after roles are removed', function () {
    auth()->user()->of($this->merchant)->assign($this->secondRole);

    auth()->user()->of($this->merchant)->removeRoles();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->exists()
    )->toBeTrue();

    $this->assertCount(2, auth()->user()->merchants()->get());

    expect(auth()->user()->of($this->merchant)->hasRole($this->role))->toBeFalse();
});

it('can set extra columns on pivot table when assigning roles', function () {
    auth()->user()->of($this->merchant)
        ->withPivot(['department' => 'sales'])
        ->assign($this->secondRole);

    expect(auth()->user()->merchants()->wherePivot('department', 'sales')->exists())->toBeTrue();
});

it('can set multiple columns on pivot table when assigning roles', function () {
    $manager = User::factory()->create();

    auth()->user()->of($this->merchant)
        ->withPivot([
            'department' => 'sales',
            'added_by' => $manager->id,
        ])
        ->assign($this->secondRole);

    expect(
        auth()->user()->merchants()
            ->wherePivot('department', 'sales')
            ->wherePivot('added_by', $manager->id)
            ->exists()
    )->toBeTrue();
});

it('can chain multiple `withPivot` method on pivot table when assigning roles', function () {
    $manager = User::factory()->create();

    auth()->user()->of($this->merchant)
        ->withPivot(['department' => 'sales'])
        ->withPivot(['added_by' => $manager->id])
        ->assign($this->secondRole);

    expect(
        auth()->user()->merchants()
            ->wherePivot('department', 'sales')
            ->wherePivot('added_by', $manager->id)
            ->exists()
    )->toBeTrue();
});
