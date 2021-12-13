<?php

use Tarzancodes\RolesAndPermissions\Tests\Models\User;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Repositories\ModelRepository;
use Tarzancodes\RolesAndPermissions\Repositories\PivotModelRepository;

beforeEach(fn () => auth()->login(User::factory()->create()));

it('uses the right repository', function () {
    expect(auth()->user()->getRepository())
        ->toBeInstanceOf(ModelRepository::class);

    expect(auth()->user()->of(Merchant::factory()->create()))
        ->toBeInstanceOf(PivotModelRepository::class);
});
