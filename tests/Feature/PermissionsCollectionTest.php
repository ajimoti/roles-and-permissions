<?php

use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;
use Tarzancodes\RolesAndPermissions\Helpers\BasePermission;
use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

test('permission collection has the right values', function () {
    $user = User::factory()->create();

    $user->assign(Role::SuperAdmin, Role::Admin);

    expect($user->permissions())->toBeInstanceOf(PermissionCollection::class);

    expect($user->permissions()->toArray())->toBe(Role::getPermissions(Role::SuperAdmin, Role::Admin)->toArray());

    $user->permissions()->each(function ($permission) {
        expect($permission)->toBeInstanceOf(BasePermission::class);
        expect($permission->value)->toBeIn(Role::getPermissions(Role::SuperAdmin, Role::Admin)->toArray());
    });
})->group('permissionCollection');

test('role collection has the right values for pivot records', function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    $user = User::factory()->create();
    $merchant = Merchant::factory()->create();

    $user->of($merchant)->assign(MerchantRole::Distributor, MerchantRole::RetailManager);

    expect($user->of($merchant)->roles())->toBeInstanceOf(RoleCollection::class);

    $user->of($merchant)->roles()->each(function ($role) {
        expect($role)->toBeInstanceOf(MerchantRole::class);

        if ($role->value === MerchantRole::Distributor) {
            expect($role->value)->toBe(MerchantRole::Distributor);
            expect($role->key)->toBe('Distributor');
            expect($role->description)->toBe('Distributes goods to customers');
            expect($role->permissions == MerchantRole::getPermissions(MerchantRole::Distributor))->toBeTrue();
        } else {
            expect($role->value)->toBe(MerchantRole::RetailManager);
            expect($role->key)->toBe('RetailManager');
            expect($role->description)->toBe('Manages products');
            expect($role->permissions == MerchantRole::getPermissions(MerchantRole::RetailManager))->toBeTrue();
        }
    });
})->group('permissionCollection');


test('can get collection of permissions from permission values', function () {
    $collection = Permission::collect(Permission::BuyProduct, Permission::DeleteProduct);

    expect($collection)->toBeInstanceOf(PermissionCollection::class);

    foreach ($collection as $permission) {
        expect($permission)->toBeInstanceOf(Permission::class);
        expect($permission)->toHaveProperties(['value', 'key', 'description', 'title']);
    }
})->group('permissionCollection');
