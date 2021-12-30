<?php

use Tarzancodes\RolesAndPermissions\Tests\Enums\MerchantRole;

beforeEach(function () {
    $this->activeRole = MerchantRole::Distributor;
});

it('active role is not returned with lower roles', function () {
    $lowerRoles = MerchantRole::select($this->activeRole)->getLowerRoles();

    expect(! in_array($this->activeRole, $lowerRoles))->toBeTrue();
});

it('active role is not returned with higher roles', function () {
    $higherRoles = MerchantRole::select($this->activeRole)->getHigherRoles();

    expect(! in_array($this->activeRole, $higherRoles))->toBeTrue();
});

it('roles are in hierarchy', function () {
    $lowerRoles = MerchantRole::select($this->activeRole)->getLowerRoles();
    $higherRoles = MerchantRole::select($this->activeRole)->getHigherRoles();

    expect($lowerRoles)->toContain(MerchantRole::RetailManager, MerchantRole::CustomerAttendant, MerchantRole::Customer);
    expect($higherRoles)->toBeEmpty();


    $lowerRoles = MerchantRole::select(MerchantRole::RetailManager)->getLowerRoles();
    $higherRoles = MerchantRole::select(MerchantRole::RetailManager)->getHigherRoles();
    expect($lowerRoles)->toContain(MerchantRole::CustomerAttendant, MerchantRole::Customer);
    expect($higherRoles)->toContain(MerchantRole::Distributor);
});
