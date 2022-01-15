<?php

use Ajimoti\RolesAndPermissions\Tests\Enums\MerchantRole;
use Ajimoti\RolesAndPermissions\Tests\Models\Merchant;
use Ajimoti\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    config()->set('roles-and-permissions.roles_enum.merchant_user', MerchantRole::class);

    $this->model = Merchant::factory()->create();
    $this->user = User::factory()->create();
    $this->role = MerchantRole::getRandomValue();
    $this->roleInstance = MerchantRole::fromValue($this->role);

    $this->user->of($this->model)->assign($this->role);
    $this->model->assign($this->role);
});

it('can check that a model has a role using magic method', function () {
    expect($this->model->hasRole($this->role))->toBeTrue();

    expect($this->model->{"is".$this->roleInstance->key}())->toBeTrue();
})->group('MagicMethods');

it('can check that a model has a permission using magic method', function () {
    expect($this->model->hasRole($this->role))->toBeTrue();

    foreach ($this->roleInstance->permissions as $permission) {
        expect($this->model->{"can".$permission->key}())->toBeTrue();
    }
})->group('MagicMethods');

it('can check that a many-to-many relationship has a role using magic method', function () {
    expect($this->user->of($this->model)->hasRole($this->role))->toBeTrue();

    expect($this->user->of($this->model)->{"is".$this->roleInstance->key}())->toBeTrue();
})->group('MagicMethods');

it('can check that a many-to-many relationship has a permission using magic method', function () {
    expect($this->user->of($this->model)->hasRole($this->role))->toBeTrue();

    foreach ($this->roleInstance->permissions as $permission) {
        expect($this->user->of($this->model)->{"can".$permission->key}())->toBeTrue();
    }
})->group('MagicMethods');

it('magic method functionality does not interfere with laravel `is()` method', function () {
    expect($this->user->is(User::factory()->create()))->toBeFalse();

    expect($this->user->is($this->user))->toBeTrue();
})->group('MagicMethods');

it('magic method functionality does not interfere with laravel `can()` method', function () {
    expect($this->model->can("a_random_permission_ha_ha_ha"))->toBeFalse();

    // Cases where MerchantRole::Customer is the assigned role,
    // the permissions list will be empty
    if ($this->roleInstance->permissions->isNotEmpty()) {
        $permission = $this->roleInstance->permissions->random();

        expect($this->model->can($permission->value))->toBeTrue();
    }
})->group('MagicMethods');
