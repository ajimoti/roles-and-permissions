<?php

use Tarzancodes\RolesAndPermissions\Repositories\ModelRepository;
use Tarzancodes\RolesAndPermissions\Repositories\PivotModelRepository;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

// beforeEach(function () {
//     auth()->login(User::factory()->create());

//     auth()->user()->merchants()->save(Merchant::factory()->create());
// });

// it('uses the right repository', function () {
//     expect(auth()->user()->getRepository())
//         ->toBeInstanceOf(ModelRepository::class);

//     expect(auth()->user()->of(Merchant::factory()->create())->getRepository())
//         ->toBeInstanceOf(PivotModelRepository::class);
// });
