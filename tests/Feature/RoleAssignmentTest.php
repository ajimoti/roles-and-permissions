<?php

use Illuminate\Support\Facades\DB;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;
use Tarzancodes\RolesAndPermissions\Tests\Models\User;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;

beforeEach(function () {
    auth()->login(User::factory()->create());
    $this->merchant = Merchant::factory()->create();
});

test('user can be assigned a single role', function () {
    $role = Role::getRandomValue();

    auth()->user()->assign($role);

    $this->assertCount(1, auth()->user()->roles());

    expect(auth()->user()->hasRole($role))->toBeTrue();
    expect(auth()->user()->roles())->toContain($role);

    expect(
        auth()->user()->modelRoles()->whereRole($role)->exists()
    )->toBeTrue();
});

test('user can be assigned multiple roles', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->assign($firstRole, $secondRole);

    $this->assertCount(2, auth()->user()->roles());

    expect(auth()->user()->hasRole($firstRole))->toBeTrue();
    expect(auth()->user()->hasRole($secondRole))->toBeTrue();
    expect(auth()->user()->hasRole($firstRole, $secondRole))->toBeTrue();
    expect(auth()->user()->hasRole($secondRole, $firstRole))->toBeTrue();
    expect(auth()->user()->roles())->toContain($firstRole, $secondRole);

    expect(
        auth()->user()->modelRoles()->whereRole($firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->modelRoles()->whereRole($secondRole)->exists()
    )->toBeTrue();
});

test('user can be assigned multiple roles as an array', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->assign([$firstRole, $secondRole]);

    $this->assertCount(2, auth()->user()->roles());

    expect(auth()->user()->roles())->toContain($secondRole, $firstRole);

    expect(auth()->user()->hasRole([$firstRole]))->toBeTrue();
    expect(auth()->user()->hasRole([$secondRole]))->toBeTrue();
    expect(auth()->user()->hasRole([$firstRole, $secondRole]))->toBeTrue();

    expect(
        auth()->user()->modelRoles()->whereRole($firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->modelRoles()->whereRole($secondRole)->exists()
    )->toBeTrue();
});

test('pivot model can be assigned a role', function (){
    $role = Role::getRandomValue();

    auth()->user()->of($this->merchant)->assign($role);

    $this->assertCount(1, auth()->user()->of($this->merchant)->roles());

    expect(auth()->user()->of($this->merchant)->roles())->toContain($role);
    expect(auth()->user()->of($this->merchant)->hasRole($role))->toBeTrue();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $role)->exists()
    )->toBeTrue();
});

test('pivot model can be assigned multiple roles', function () {
    $firstRole = Role::getRandomValue();
    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->of($this->merchant)->assign($firstRole, $secondRole);

    $this->assertCount(2, auth()->user()->of($this->merchant)->roles());

    expect(auth()->user()->of($this->merchant)->hasRole($firstRole, $secondRole))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->hasRole($secondRole, $firstRole))->toBeTrue();

    expect(auth()->user()->of($this->merchant)->roles())->toContain($firstRole);
    expect(auth()->user()->of($this->merchant)->roles())->toContain($secondRole);
    expect(auth()->user()->of($this->merchant)->roles())->toContain($secondRole, $firstRole);

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $secondRole)->exists()
    )->toBeTrue();
});

test('pivot model can be assigned multiple roles as an array', function () {
    $firstRole = Role::getRandomValue();

    do {
        $secondRole = Role::getRandomValue();
    } while ($firstRole === $secondRole);

    auth()->user()->of($this->merchant)->assign([$firstRole, $secondRole]);

    $this->assertCount(2, auth()->user()->of($this->merchant)->roles());

    expect(auth()->user()->of($this->merchant)->hasRole([$firstRole, $secondRole]))->toBeTrue();
    expect(auth()->user()->of($this->merchant)->hasRole([$secondRole, $secondRole]))->toBeTrue();

    expect(auth()->user()->of($this->merchant)->roles())->toContain($firstRole);
    expect(auth()->user()->of($this->merchant)->roles())->toContain($secondRole);
    expect(auth()->user()->of($this->merchant)->roles())->toContain($secondRole, $firstRole);

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $firstRole)->exists()
    )->toBeTrue();

    expect(
        auth()->user()->merchants()->wherePivot('merchant_id', $this->merchant->id)->wherePivot(config('roles-and-permissions.pivot.column_name'), $secondRole)->exists()
    )->toBeTrue();
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
