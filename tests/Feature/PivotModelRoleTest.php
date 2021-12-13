<?php

use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;

// beforeEach(function () {
//     auth()->login(User::factory()->create());
//     $this->merchant = Merchant::factory()->create();

//     $this->role = Role::getRandomValue();

//     do {
//         $this->secondRole = Role::getRandomValue();
//     } while ($this->role == $this->secondRole);

//     auth()->user()->assign($this->role);
// });

// test('user has role permissions', function () {
//     expect(auth()->user()->has(Role::getPermissions($this->role)))->toBeTrue();
// });

// test('user can access every permission belonging to the given role', function () {
//     foreach (Role::getPermissions($this->role) as $permission) {
//         expect(auth()->user()->can($permission))->toBeTrue();
//     }
// });

// test('user is not given other roles permissions', function () {
//     foreach (Role::all() as $role) {
//         if ($role != $this->role) {
//             expect(auth()->user()->has(Role::getPermissions($role)))->toBeFalse();
//         }
//     }
// });

// test('role authorization for specified role on user to be true', function () {
//     expect(auth()->user()->authorizeRole($this->role))->toBeTrue();
// });

// test('role authorization for other roles to throw exception', function () {
//     foreach (Role::all() as $role) {
//         if ($role != $this->role) {
//             expect(fn() => auth()->user()->authorizeRole($role))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
//         }
//     }
// });

// test('user with existing role can be assigned other role', function () {
//     auth()->user()->assign($this->secondRole);

//     expect(auth()->user()->hasRole($this->secondRole))->toBeTrue();
// });

// test('user can perform permissions of new roles and older role', function () {
//     auth()->user()->assign($this->secondRole);

//     foreach (Role::getPermissions($this->secondRole) as $permission) {
//         expect(auth()->user()->can($permission))->toBeTrue();
//     }

//     foreach (Role::getPermissions($this->role) as $permission) {
//         expect(auth()->user()->can($permission))->toBeTrue();
//     }
// });

// test('user has permissions of new roles and older role', function () {
//     auth()->user()->assign($this->secondRole);

//     expect(auth()->user()->has(Role::getPermissions($this->role)))->toBeTrue();
//     expect(auth()->user()->has(Role::getPermissions($this->secondRole)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions($this->role, $this->secondRole)))->toBeTrue();
//     expect(auth()->user()->has(Role::getPermissions($this->secondRole, $this->role)))->toBeTrue();
// });

