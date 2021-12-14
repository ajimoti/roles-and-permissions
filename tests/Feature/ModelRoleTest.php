<?php

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    auth()->login(User::factory()->create());
    $this->role = Role::getRandomValue();

    do {
        $this->secondRole = Role::getRandomValue();
    } while ($this->role == $this->secondRole);

    auth()->user()->assign($this->role);
});

it('has role permissions', function () {
    expect(auth()->user()->has(Role::getPermissions($this->role)))->toBeTrue();
});

it('can access every permission belonging to the given role', function () {
    foreach (Role::getPermissions($this->role) as $permission) {
        expect(auth()->user()->can($permission))->toBeTrue();
    }
});

it('is not given other roles permissions', function () {
    foreach (Role::all() as $role) {
        if ($role != $this->role) {
            expect(auth()->user()->has(Role::getPermissions($role)))->toBeFalse();
        }
    }
});

it('role authorization is valid', function () {
    expect(auth()->user()->authorizeRole($this->role))->toBeTrue();
});

it('role authorization for other roles to throw exception', function () {
    $otherRoles = [];
    foreach (Role::all() as $role) {
        if ($role != $this->role) {
            $otherRoles[] = $role;
            expect(fn () => auth()->user()->authorizeRole($role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => auth()->user()->authorizeRole($otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can be assigned new roles', function () {
    auth()->user()->assign($this->secondRole);

    expect(auth()->user()->hasRole($this->secondRole))->toBeTrue();
});

it('can perform permissions of new roles and previous roles', function () {
    auth()->user()->assign($this->secondRole);

    foreach (Role::getPermissions($this->secondRole) as $permission) {
        expect(auth()->user()->can($permission))->toBeTrue();
    }

    foreach (Role::getPermissions($this->role) as $permission) {
        expect(auth()->user()->can($permission))->toBeTrue();
    }
});

it('has permissions of new roles and older role', function () {
    auth()->user()->assign($this->secondRole);

    expect(auth()->user()->has(Role::getPermissions($this->role)))->toBeTrue();
    expect(auth()->user()->has(Role::getPermissions($this->secondRole)))->toBeTrue();

    expect(auth()->user()->has(Role::getPermissions($this->role, $this->secondRole)))->toBeTrue();
    expect(auth()->user()->has(Role::getPermissions($this->secondRole, $this->role)))->toBeTrue();
});

it('role authorization for multiple roles are valid', function () {
    expect(auth()->user()->authorizeRole($this->role, $this->role))->toBeTrue();
});

it('role authorization for other unassigned roles to throw exception', function () {
    $otherRoles = [];
    foreach (Role::all() as $role) {
        if (! in_array($role, [$this->role, $this->secondRole])) {
            $otherRoles[] = $role;
            expect(fn () => auth()->user()->authorizeRole($role, $this->role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => auth()->user()->authorizeRole($otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can remove specific role', function () {
    auth()->user()->assign($this->secondRole);

    auth()->user()->removeRoles($this->secondRole);

    expect(auth()->user()->hasRole($this->secondRole))->toBeFalse();
});

it('can remove multiple roles', function () {
    auth()->user()->assign($this->secondRole);

    auth()->user()->removeRoles($this->secondRole, $this->role);

    expect(auth()->user()->hasRole($this->secondRole, $this->role))->toBeFalse();
    expect(auth()->user()->hasRole($this->role))->toBeFalse();
    expect(auth()->user()->hasRole($this->secondRole))->toBeFalse();
});

it('can remove all roles', function () {
    auth()->user()->assign($this->secondRole);

    auth()->user()->removeRoles();

    expect(auth()->user()->hasRole($this->role))->toBeFalse();

    expect(auth()->user()->hasRole($this->secondRole))->toBeFalse();

    expect(auth()->user()->hasRole($this->role, $this->secondRole))->toBeFalse();

    expect(fn () => auth()->user()->authorizeRole($this->role, $this->secondRole))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});
