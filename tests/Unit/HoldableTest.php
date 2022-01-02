<?php

use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;

beforeEach(function () {
    $this->activeRole = MerchantRole::Distributor;
});

it('active role is not returned with lower roles', function () {
    $lowerRoles = MerchantRole::hold($this->activeRole)->getLowerRoles();

    expect(! in_array($this->activeRole, $lowerRoles))->toBeTrue();

})->group('holdable');

it('active role is not returned with higher roles', function () {
    $higherRoles = MerchantRole::hold($this->activeRole)->getHigherRoles();

    expect(! in_array($this->activeRole, $higherRoles))->toBeTrue();
})->group('holdable');

it('roles are in hierarchy', function () {
    $lowerRoles = MerchantRole::hold($this->activeRole)->getLowerRoles();
    $higherRoles = MerchantRole::hold($this->activeRole)->getHigherRoles();

    expect($lowerRoles)->toContain(MerchantRole::RetailManager, MerchantRole::CustomerAttendant, MerchantRole::Customer);
    expect($higherRoles)->toBeEmpty();

    $lowerRoles = MerchantRole::hold(MerchantRole::RetailManager)->getLowerRoles();
    $higherRoles = MerchantRole::hold(MerchantRole::RetailManager)->getHigherRoles();
    expect($lowerRoles)->toContain(MerchantRole::CustomerAttendant, MerchantRole::Customer);
    expect($higherRoles)->toContain(MerchantRole::Distributor);
})->group('holdable');
