<?php

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

it('singular hasRole method works', function () {
    expect($this->model->hasRole($this->role))->toBeTrue();
})->group('duplicateMethods');

it('plural hasRole method works', function () {
    expect($this->model->hasRoles($this->role))->toBeTrue();

    $this->model->assign($this->secondRole);
    expect($this->model->hasRoles($this->role, $this->secondRole))->toBeTrue();
    expect($this->model->hasRoles([$this->secondRole, $this->role]))->toBeTrue();
})->group('duplicateMethods');
