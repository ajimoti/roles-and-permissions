<?php

use Tarzancodes\RolesAndPermissions\Repositories\BelongsToManyRepository;
use Tarzancodes\RolesAndPermissions\Repositories\ModelRepository;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(fn () => auth()->login(User::factory()->create()));

it('uses the right repository', function () {
    expect(auth()->user()->getRepository())
        ->toBeInstanceOf(ModelRepository::class);

    expect(auth()->user()->of(Merchant::factory()->create()))
        ->toBeInstanceOf(BelongsToManyRepository::class);
})->group('repository');
