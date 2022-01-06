<?php

use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;
use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

test('role collection has the right values', function () {
    $user = User::factory()->create();

    $user->assign(Role::SuperAdmin, Role::Admin);

    expect($user->roles())->toBeInstanceOf(RoleCollection::class);

    $user->roles()->each(function ($role) {
        expect($role)->toBeInstanceOf(Role::class);
        expect($role->permissions)->toBeInstanceOf(PermissionCollection::class);

        if ($role->value === Role::SuperAdmin) {
            expect($role->value)->toBe(Role::SuperAdmin);
            expect($role->key)->toBe('SuperAdmin');
            expect($role->description)->toBe('Super admin');
            expect($role->permissions == Role::getPermissions(Role::SuperAdmin))->toBeTrue();
        } else {
            expect($role->value)->toBe(Role::Admin);
            expect($role->key)->toBe('Admin');
            expect($role->description)->toBe('Admin');
            expect($role->permissions == Role::getPermissions(Role::Admin))->toBeTrue();
        }
    });
})->group('roleCollection');

test('role collection has the right values for pivot records', function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    $user = User::factory()->create();
    $merchant = Merchant::factory()->create();

    $user->of($merchant)->assign(MerchantRole::Distributor, MerchantRole::RetailManager);

    expect($user->of($merchant)->roles())->toBeInstanceOf(RoleCollection::class);

    $user->of($merchant)->roles()->each(function ($role) {
        expect($role)->toBeInstanceOf(MerchantRole::class);
        expect($role->permissions)->toBeInstanceOf(PermissionCollection::class);

        if ($role->value === MerchantRole::Distributor) {
            expect($role->value)->toBe(MerchantRole::Distributor);
            expect($role->key)->toBe('Distributor');
            expect($role->description)->toBe('Distributes goods to customers');
            expect($role->permissions == MerchantRole::getPermissions(MerchantRole::Distributor));
        } else {
            expect($role->value)->toBe(MerchantRole::RetailManager);
            expect($role->key)->toBe('RetailManager');
            expect($role->description)->toBe('Manages products');
            expect($role->permissions == MerchantRole::getPermissions(MerchantRole::RetailManager));
        }
    });
})->group('roleCollection');

test('can get collection of roles from role values', function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    $collection = MerchantRole::collect(MerchantRole::Distributor, MerchantRole::RetailManager);

    expect($collection)->toBeInstanceOf(RoleCollection::class);

    foreach ($collection as $role) {
        expect($role)->toBeInstanceOf(MerchantRole::class);
        expect($role)->toHaveProperties(['value', 'key', 'description', 'permissions', 'title']);
    }
})->group('roleCollection');
