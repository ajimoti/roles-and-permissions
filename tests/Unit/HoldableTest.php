<?php

use Ajimoti\RolesAndPermissions\Tests\Enums\MerchantRole;

beforeEach(function () {
    $this->activeRole = MerchantRole::Distributor;
});

it('active role is not returned with lower roles', function () {
    $lowerRoles = MerchantRole::hold($this->activeRole)->getLowerRoles();

    expect($lowerRoles->contains(function ($value, $key) {
        return $value->value === $this->activeRole;
    }))->toBeFalse();

    expect($lowerRoles->count())->toBeGreaterThan(0);
    expect($lowerRoles->count())->toBeLessThan(MerchantRole::all()->count());
})->group('holdable');

it('active role is not returned with higher roles', function () {
    $higherRoles = MerchantRole::hold($this->activeRole)->getHigherRoles();

    expect($higherRoles->contains(function ($value, $key) {
        return $value->value === $this->activeRole;
    }))->toBeFalse();
})->group('holdable');

it('roles are in hierarchy', function () {
    $lowerRoles = MerchantRole::hold($this->activeRole)->getLowerRoles();
    $higherRoles = MerchantRole::hold($this->activeRole)->getHigherRoles();

    expect($lowerRoles->contains(function ($value, $key) {
        return in_array($value->value, [MerchantRole::RetailManager, MerchantRole::CustomerAttendant, MerchantRole::Customer]);
    }))->toBeTrue();

    expect($higherRoles)->toBeEmpty();

    $lowerRoles = MerchantRole::hold(MerchantRole::RetailManager)->getLowerRoles();
    $higherRoles = MerchantRole::hold(MerchantRole::RetailManager)->getHigherRoles();

    expect($lowerRoles->contains(function ($value, $key) {
        return in_array($value->value, [MerchantRole::CustomerAttendant, MerchantRole::Customer]);
    }))->toBeTrue();

    expect($higherRoles->contains(function ($value, $key) {
        return $value->value === MerchantRole::Distributor;
    }))->toBeTrue();
})->group('holdable');
