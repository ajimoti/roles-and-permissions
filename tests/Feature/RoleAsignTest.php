<?php

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;

beforeEach(function () {
    auth()->login(User::factory()->create());

    auth()->user()->merchants()->save(Merchant::factory()->create());

    $this->merchant = auth()->user()->merchants()->first();

    // Remove other roles
    auth()->user()->removeRoles();
    auth()->user()->of($this->merchant)->removeRoles();
});

test('user can be assigned a single role', function () {
    auth()->user()->assign(Role::SuperAdmin);

    $this->assertCount(1, auth()->user()->roles());

    expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->hasRole(Role::Customer))->toBeFalse();
    expect(auth()->user()->roles())->toContain(Role::SuperAdmin);
    expect(auth()->user()->authorizeRole(Role::SuperAdmin))->toBeTrue();
    expect(fn () => auth()->user()->authorize(Role::getPermissions(Role::Customer)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

test('user can be assigned multiple roles', function () {
    auth()->user()->assign(Role::SuperAdmin, Role::Admin);

    $this->assertCount(2, auth()->user()->roles());

    expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->hasRole(Role::Admin))->toBeTrue();
    expect(auth()->user()->hasRole(Role::SuperAdmin, Role::Admin))->toBeTrue();
    expect(auth()->user()->hasRole(Role::Customer))->toBeFalse();
    expect(auth()->user()->roles())->toContain(Role::SuperAdmin, Role::Admin);
    expect(auth()->user()->authorizeRole(Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::Admin))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::Admin, Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::SuperAdmin, Role::Admin))->toBeTrue();

    expect(fn () => auth()->user()->authorize(Role::getPermissions(Role::Customer)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

test('user can be assigned multiple roles as an array', function () {
    auth()->user()->assign([Role::SuperAdmin, Role::Customer]);

    $this->assertCount(2, auth()->user()->roles());

    expect(auth()->user()->roles())->toContain(Role::SuperAdmin, Role::Customer);

    expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->hasRole(Role::Customer))->toBeTrue();
    expect(auth()->user()->hasRole(Role::SuperAdmin, Role::Customer))->toBeTrue();
    expect(auth()->user()->hasRole(Role::Admin))->toBeFalse();

    expect(auth()->user()->authorizeRole(Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::Customer))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::Customer, Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->authorizeRole(Role::SuperAdmin, Role::Customer))->toBeTrue();

    expect(fn () => auth()->user()->authorize(Role::getPermissions(Role::Admin)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

test('pivot model can be assigned a role', function () {
    auth()->user()->of($this->merchant)->assign(Role::Admin);

    $this->assertCount(1, auth()->user()->of($this->merchant)->roles());

    expect(auth()->user()->of($this->merchant)->roles())->toContain(Role::Admin);

    expect(auth()->user()->of($this->merchant)->hasRole(Role::Admin))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->hasRole(Role::SuperAdmin))->toBeFalse();

    expect(auth()->user()->of($this->merchant)->authorizeRole(Role::Admin))->toBeTrue();
    expect(fn () => auth()->user()->of($this->merchant)->authorize(Role::getPermissions(Role::Customer)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});

test('pivot model can be assigned multiple roles', function () {
    auth()->user()->of($this->merchant)->assign(Role::SuperAdmin, Role::Admin);

    $this->assertCount(2, auth()->user()->of($this->merchant)->roles());
    $this->assertEquals(auth()->user()->of($this->merchant)->permissions(), Role::getPermissions(Role::SuperAdmin, Role::Admin));

    expect(auth()->user()->of($this->merchant)->hasRole(Role::Admin, Role::SuperAdmin))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->hasRole(Role::SuperAdmin, Role::Admin))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->hasRole(Role::Customer, Role::Admin))->toBeFalse();

    expect(auth()->user()->of($this->merchant)->roles())->toContain(Role::Admin);
    expect(auth()->user()->of($this->merchant)->roles())->toContain(Role::SuperAdmin, Role::Admin);

    expect(auth()->user()->of($this->merchant)->authorizeRole(Role::Admin))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->authorizeRole(Role::SuperAdmin, Role::Admin))->toBeTrue();

    expect(fn () => auth()->user()->of($this->merchant)->authorize(Role::getPermissions(Role::Customer)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
    expect(fn () => auth()->user()->of($this->merchant)->authorize(Role::getPermissions(Role::Customer)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
});


// =====================================================================================================================

// test('user can be assigned a single role', function () {
//     // Remove other roles
//     auth()->user()->removeRoles();

//     auth()->user()->assign(Role::SuperAdmin);

//     $this->assertCount(1, auth()->user()->roles());

//     $this->assertEquals(auth()->user()->permissions(), Role::getPermissions(Role::SuperAdmin));

//     expect(auth()->user()->can(Permission::DeleteProduct))->toBeTrue();

//     expect(auth()->user()->can(Permission::MarkAsSoldOut))->toBeFalse();

//     expect(auth()->user()->has(Role::getPermissions(Role::SuperAdmin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::Admin)))->toBeFalse();

//     expect(auth()->user()->roles()[0])->toBe(Role::SuperAdmin);

//     expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();

//     expect(auth()->user()->authorizeRole(Role::SuperAdmin))->toBeTrue();

//     expect(auth()->user()->authorizeRole(Role::SuperAdmin))->toBeTrue();
// });

// test('user can be assigned multiple roles', function () {
//     // Remove previous roles if any
//     auth()->user()->removeRoles();

//     auth()->user()->assign(Role::SuperAdmin, Role::Admin);

//     $this->assertCount(2, auth()->user()->roles());

//     $this->assertEquals(auth()->user()->permissions(), Role::getPermissions(Role::SuperAdmin, Role::Admin));

//     expect(auth()->user()->can(Permission::DeleteProduct))->toBeTrue();

//     expect(auth()->user()->can(Permission::MarkAsSoldOut))->toBeTrue();

//     expect(auth()->user()->can(Permission::BuyProduct))->toBeFalse();

//     expect(auth()->user()->has(Role::getPermissions(Role::SuperAdmin, Role::Admin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::Admin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::SuperAdmin)))->toBeTrue();

//     expect(Check::all([Role::SuperAdmin, Role::Admin])->existsIn(auth()->user()->roles()))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::Admin))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::Admin, Role::SuperAdmin))->toBeTrue();

//     expect(auth()->user()->authorizeRole(Role::Admin))->toBeTrue();

//     expect(auth()->user()->authorize(Role::getPermissions(Role::Admin)))->toBeTrue();

//     expect(fn() => auth()->user()->authorizeRole(Role::SuperAdmin, Role::Customer))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');

//     expect(fn() => auth()->user()->authorize(Role::getPermissions(Role::SuperAdmin)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
// });

// test('pivot model can be assigned a role', function () {
//     // Remove previous roles if any
//     auth()->user()->of($this->merchant)->removeRoles();

//     auth()->user()->of($this->merchant)->assign(Role::Admin);

//     $this->assertCount(1, auth()->user()->of($this->merchant)->roles());

//     $this->assertEquals(auth()->user()->of($this->merchant)->permissions(), Role::getPermissions(Role::Admin));

//     expect(auth()->user()->can(Permission::MarkAsSoldOut))->toBeTrue();

//     expect(auth()->user()->can(Permission::DeleteProduct))->toBeFalse();

//     expect(auth()->user()->has(Role::getPermissions(Role::Admin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::Customer)))->toBeFalse();

//     expect(auth()->user()->roles()[0])->toBe(Role::Admin);

//     expect(auth()->user()->hasRole(Role::Admin))->toBeTrue();

//     expect(auth()->user()->authorizeRole(Role::Admin))->toBeTrue();

//     expect(auth()->user()->authorize(Role::getPermissions(Role::Admin)))->toBeTrue();

//     expect(fn() => auth()->user()->authorizeRole(Role::SuperAdmin, Role::Customer))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');

//     expect(fn() => auth()->user()->authorize(Role::getPermissions(Role::SuperAdmin)))->toThrow(PermissionDeniedException::class, 'You are not authorized to perform this action.');
// });

// test('pivot model can be assigned multiple roles', function () {
//     // Remove previous roles if any
//     auth()->user()->of($this->merchant)->removeRoles();

//     auth()->user()->of($this->merchant)->assign(Role::SuperAdmin, Role::Admin);

//     $this->assertCount(2, auth()->user()->roles());

//     $this->assertEquals(auth()->user()->permissions(), Role::getPermissions(Role::SuperAdmin, Role::Admin));

//     expect(auth()->user()->can(Permission::DeleteProduct))->toBeTrue();

//     expect(auth()->user()->can(Permission::MarkAsSoldOut))->toBeTrue();

//     expect(auth()->user()->can(Permission::BuyProduct))->toBeFalse();

//     expect(auth()->user()->has(Role::getPermissions(Role::SuperAdmin, Role::Admin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::Admin)))->toBeTrue();

//     expect(auth()->user()->has(Role::getPermissions(Role::SuperAdmin)))->toBeTrue();

//     expect(Check::all([Role::SuperAdmin, Role::Admin])->existsIn(auth()->user()->roles()))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::SuperAdmin))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::Admin))->toBeTrue();

//     expect(auth()->user()->hasRole(Role::Admin, Role::SuperAdmin))->toBeTrue();
// });
