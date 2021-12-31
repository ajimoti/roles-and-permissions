<?php

use Tarzancodes\RolesAndPermissions\Exceptions\InvalidArgumentException;
use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;

it('role with zero permissions should not throw errors', function () {
    $model = Merchant::factory()->create();
    $role = MerchantRole::Customer;

    $model->assign($role);

    expect(fn () => $model->has(MerchantRole::getPermissions($role)))->toThrow(InvalidArgumentException::class, 'expects at least one parameter');
});