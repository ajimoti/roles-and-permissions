<?php

use Ajimoti\RolesAndPermissions\Exceptions\InvalidArgumentException;
use Ajimoti\RolesAndPermissions\Tests\Enums\MerchantRole;
use Ajimoti\RolesAndPermissions\Tests\Models\Merchant;

it('role with zero permissions should not throw errors', function () {
    $model = Merchant::factory()->create();
    $role = MerchantRole::Customer;

    $model->assign($role);

    expect(fn () => $model->holds(MerchantRole::getPermissions($role)))->toThrow(InvalidArgumentException::class, 'expects at least one parameter');
})->group('role');

it('can get role instance from values', function () {
    $roleInstances = MerchantRole::getInstancesFromValues(MerchantRole::RetailManager, MerchantRole::CustomerAttendant);

    foreach ($roleInstances as $instance) {
        expect($instance)->toBeInstanceOf(MerchantRole::class);
        expect($instance)->toHaveProperties(['value', 'key', 'description', 'permissions']);
    }
})->group('role');
