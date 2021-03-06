<?php

use Ajimoti\RolesAndPermissions\Exceptions\PermissionDeniedException;
use Ajimoti\RolesAndPermissions\Tests\Enums\Permission;
use Ajimoti\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    $this->model = User::factory()->create();
    $this->permission = Permission::getRandomValue();

    do {
        $this->secondPermission = Permission::getRandomValue();
    } while ($this->permission == $this->secondPermission);
});

it('a model can be given a permission', function () {
    expect($this->model->give($this->permission))->toBeTrue();

    expect($this->model->modelPermissions()->first()->permission)->toBe($this->permission);

    expect($this->model->permissions()->first()->value)->toBe($this->permission);

    expect($this->model->holds($this->permission))->toBeTrue();

    expect($this->model->holds(Permission::collect($this->permission)))->toBeTrue();

    expect($this->model->holds(Permission::getInstancesFromValues($this->permission)))->toBeTrue();

    expect($this->model->authorize(Permission::getInstancesFromValues($this->permission)))->toBeTrue();

    expect($this->model->authorize($this->permission))->toBeTrue();
})->group('directPermissions');

it('can fetch direct permissions', function () {
    expect($this->model->give($this->permission))->toBeTrue();

    expect($this->model->directPermissions()->first()->value)->toBe($this->permission);
})->group('directPermissions');

it('can revoke direct permission', function () {
    expect($this->model->give($this->permission))->toBeTrue();

    expect($this->model->revoke($this->permission))->toBeTrue();

    expect($this->model->modelPermissions->isEmpty())->toBeTrue();

    expect($this->model->holds($this->permission))->toBeFalse();

    expect($this->model->holds(Permission::collect($this->permission)))->toBeFalse();

    expect($this->model->holds(Permission::getInstancesFromValues($this->permission)))->toBeFalse();

    expect(fn () => $this->model->authorize($this->permission))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
})->group('directPermissions');
