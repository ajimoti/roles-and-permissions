<?php

use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;
use Tarzancodes\RolesAndPermissions\Exceptions\InvalidRelationNameException;
use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;
use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    // In this test, we're going to ignore the MerchantRole::Customer role when picking random roles.
    // Because it has zero permissions, and might alter the behavior of the test results
    $this->model = Merchant::factory()->create();

    $this->role = MerchantRole::getRandomValue();

    do {
        $this->role = MerchantRole::getRandomValue();
    } while ($this->role == MerchantRole::Customer);

    do {
        $this->secondRole = MerchantRole::getRandomValue();
    } while (in_array($this->secondRole, [$this->role, MerchantRole::Customer]));

    $this->model->assign($this->role);
});

it('has role permissions', function () {
    expect($this->model->holds(MerchantRole::getPermissions($this->role)->toArray()))->toBeTrue();
});

it('has lower roles permissions', function () {
    $lowerRoles = MerchantRole::hold($this->role)->getLowerRoles();

    // When the role is the lowest role,
    // it will not have any lower role
    if (! empty($lowerRoles)) {
        expect($this->model->hasRole($lowerRoles))->toBeFalse();
    }

    foreach ($lowerRoles as $role) {
        if ($role->permissions->isEmpty()) {
            // Cases where MerchantRole::Customer is one of the lower roles
            continue;
        }

        expect($role->permissions)->toBeInstanceOf(PermissionCollection::class);
        expect($this->model->holds($role->permissions))->toBeTrue();
    }
});

it('does not have higher roles permissions', function () {
    $higherRoles = MerchantRole::hold($this->role)->getHigherRoles();

    // Doing this to stop `pest` from complaining about zero assertions
    // When the role is the top role, it will not have any higher role
    if (empty($higherRoles)) {
        expect(true)->toBeTrue();

        return;
    }

    expect($this->model->hasRole($higherRoles))->toBeFalse();

    foreach ($higherRoles as $role) {
        if ($role->permissions->isEmpty()) {
            // Cases where MerchantRole::Customer is one of the higher roles
            continue;
        }

        expect($this->model->holds($role->permissions))->toBeFalse();
    }
});

it('relationship name can be passed for pivot relation', function () {
    $user = User::factory()->create();
    expect(fn () => $this->model->of($user)->assign($this->secondRole))->toThrow(InvalidRelationNameException::class);

    expect($this->model->of($user, 'merchantUsers')->assign($this->secondRole))->toBeTrue();
    expect($this->model->of($user, 'merchantUsers')->hasRole($this->secondRole))->toBeTrue();
    expect($this->model->of($user, 'merchantUsers')->holds(MerchantRole::getPermissions($this->secondRole)))->toBeTrue();
});

it('can access every permission that belongs to the given role', function () {
    foreach (MerchantRole::getPermissions($this->role) as $permission) {
        expect($this->model->can($permission))->toBeTrue();
    }
});

it('role authorization is valid', function () {
    expect($this->model->authorizeRole($this->role))->toBeTrue();
});

it('role authorization for other roles to throw exception', function () {
    $otherRoles = [];
    foreach (MerchantRole::all()->toArray() as $role) {
        if ($role != $this->role) {
            $otherRoles[] = $role;
            expect(fn () => $this->model->authorizeRole($role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => $this->model->authorizeRole($otherRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can be assigned new roles', function () {
    $this->model->assign($this->secondRole);

    expect($this->model->hasRole($this->secondRole))->toBeTrue();
});

it('can perform permissions of newly assigned role and previous roles', function () {
    $this->model->assign($this->secondRole);

    foreach (MerchantRole::getPermissions($this->secondRole) as $permission) {
        expect($this->model->can($permission))->toBeTrue();
    }

    foreach (MerchantRole::getPermissions($this->role) as $permission) {
        expect($this->model->can($permission))->toBeTrue();
    }
});

it('has permissions of a newly assigned role and older role', function () {
    $this->model->assign($this->secondRole);

    expect($this->model->holds(MerchantRole::getPermissions($this->role)))->toBeTrue();
    expect($this->model->holds(MerchantRole::getPermissions($this->secondRole)))->toBeTrue();

    expect($this->model->holds(MerchantRole::getPermissions($this->role, $this->secondRole)))->toBeTrue();
    expect($this->model->holds(MerchantRole::getPermissions($this->secondRole, $this->role)))->toBeTrue();
});

it('role authorization for multiple roles are valid', function () {
    $this->model->assign($this->secondRole);

    expect($this->model->authorizeRole($this->role, $this->secondRole))->toBeTrue();
});

it('role authorization for other unassigned roles to throw exception', function () {
    $this->model->assign($this->secondRole);

    $unassignedRoles = [];
    foreach (MerchantRole::all()->toArray() as $role) {
        if (! in_array($role, [$this->role, $this->secondRole])) {
            $unassignedRoles[] = $role;
            expect(fn () => $this->model->authorizeRole($role, $this->role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
        }
    }

    expect(fn () => $this->model->authorizeRole($unassignedRoles))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

it('can delete pivot record on role remove', function () {
    $user = User::factory()->create();

    $this->model->of($user, 'merchantUsers')->assign($this->secondRole);

    expect(
        $this->model->merchantUsers()->whereUserId($user->id)->whereRole($this->secondRole)->exists()
    )->toBeTrue();

    $this->model->of($user, 'merchantUsers')->removeRoles($this->secondRole);

    expect(
        $this->model->merchantUsers()->whereUserId($user->id)->whereRole($this->secondRole)->exists()
    )->toBeFalse();
});

it('can remove specific role', function () {
    $this->model->assign($this->secondRole);

    $this->model->removeRoles($this->secondRole);

    expect($this->model->hasRole($this->secondRole))->toBeFalse();
});

it('can remove multiple roles', function () {
    $this->model->assign($this->secondRole);

    $this->model->removeRoles($this->secondRole, $this->role);

    expect($this->model->hasRole($this->secondRole, $this->role))->toBeFalse();
    expect($this->model->hasRole($this->role))->toBeFalse();
    expect($this->model->hasRole($this->secondRole))->toBeFalse();
});

it('can remove all roles', function () {
    $this->model->assign($this->secondRole);

    $this->model->removeRoles();

    expect($this->model->hasRole($this->role))->toBeFalse();

    expect($this->model->hasRole($this->secondRole))->toBeFalse();

    expect($this->model->hasRole($this->role, $this->secondRole))->toBeFalse();

    expect(fn () => $this->model->authorizeRole($this->role, $this->secondRole))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});
