<?php

use Ajimoti\RolesAndPermissions\Tests\Enums\MerchantRole;
use Ajimoti\RolesAndPermissions\Tests\Models\Merchant;
use Ajimoti\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    $this->model = Merchant::factory()->create();
    $this->user = User::factory()->create();
    $this->role = MerchantRole::getRandomValue();

    do {
        $this->secondRole = MerchantRole::getRandomValue();
    } while ($this->role == $this->secondRole);

    $this->user->of($this->model)->assign($this->role, $this->secondRole);
    $this->model->assign($this->role, $this->secondRole);
});

it('singular hasRole method works', function () {
    expect($this->model->hasRole($this->role))->toBeTrue();
    expect($this->user->of($this->model)->hasRole($this->role))->toBeTrue();
})->group('duplicateMethods');

it('plural hasRoles method works', function () {
    expect($this->model->hasRoles($this->role))->toBeTrue();
    expect($this->model->hasRoles($this->role, $this->secondRole))->toBeTrue();
    expect($this->model->hasRoles([$this->secondRole, $this->role]))->toBeTrue();

    expect($this->user->of($this->model)->hasRoles($this->secondRole, $this->role))->toBeTrue();
    expect($this->user->of($this->model)->hasRoles([$this->secondRole, $this->role]))->toBeTrue();
})->group('duplicateMethods');

it('singular authorizeRole method works', function () {
    expect($this->model->authorizeRole($this->role))->toBeTrue();
    expect($this->user->of($this->model)->authorizeRole($this->secondRole))->toBeTrue();

    // with multiple roles
    expect($this->model->authorizeRole([$this->role, $this->secondRole]))->toBeTrue();
    expect($this->user->of($this->model)->authorizeRole([$this->secondRole, $this->role]))->toBeTrue();
})->group('duplicateMethods');

it('plural authorizeRoles method works', function () {
    expect($this->model->authorizeRoles($this->role))->toBeTrue();
    expect($this->user->of($this->model)->authorizeRoles($this->secondRole))->toBeTrue();

    // with multiple roles
    expect($this->model->authorizeRoles([$this->role, $this->secondRole]))->toBeTrue();
    expect($this->user->of($this->model)->authorizeRoles([$this->secondRole, $this->role]))->toBeTrue();
})->group('duplicateMethods');
