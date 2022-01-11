<?php

use Ajimoti\RolesAndPermissions\Repositories\BelongsToManyRepository;
use Ajimoti\RolesAndPermissions\Repositories\ModelRepository;
use Ajimoti\RolesAndPermissions\Tests\Models\Merchant;
use Ajimoti\RolesAndPermissions\Tests\Models\User;

beforeEach(fn () => auth()->login(User::factory()->create()));

it('uses the right repository', function () {
    expect(auth()->user()->repository())
        ->toBeInstanceOf(ModelRepository::class);

    expect(auth()->user()->of(Merchant::factory()->create()))
        ->toBeInstanceOf(BelongsToManyRepository::class);
})->group('repository');
