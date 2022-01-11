<?php

namespace Ajimoti\RolesAndPermissions;

use Ajimoti\RolesAndPermissions\Commands\GeneratePermissionFile;
use Ajimoti\RolesAndPermissions\Commands\GenerateRoleFile;
use Ajimoti\RolesAndPermissions\Commands\RolesAndPermissionsCommand;
use Ajimoti\RolesAndPermissions\Helpers\Check;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RolesAndPermissionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $this->app->bind('check', function ($app) {
            return new Check();
        });

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $package
            ->name('roles-and-permissions')
            ->hasConfigFile()
            ->hasCommands([RolesAndPermissionsCommand::class, GenerateRoleFile::class, GeneratePermissionFile::class]);
    }
}
