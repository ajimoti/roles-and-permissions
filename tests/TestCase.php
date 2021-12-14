<?php

namespace Tarzancodes\RolesAndPermissions\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Tarzancodes\RolesAndPermissions\RolesAndPermissionsServiceProvider;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Role;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Tarzancodes\\RolesAndPermissions\\Tests\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RolesAndPermissionsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('roles-and-permissions.roles_enum.default', Role::class);
        config()->set('roles-and-permissions.roles_enum.merchants', Role::class);
        config()->set('roles-and-permissions.pivot.column_name', 'role');

        include_once __DIR__ . '/Migrations/create_users_table.php.stub';
        include_once __DIR__ . '/Migrations/create_merchants_table.php.stub';
        include_once __DIR__ . '/Migrations/create_merchant_user_table.php.stub';

        (new \CreateUsersTable())->up();
        (new \CreateMerchantsTable())->up();
        (new \CreateMerchantUserTable())->up();
    }
}
