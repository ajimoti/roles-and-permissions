<?php

namespace Tarzancodes\RolesAndPermissions;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tarzancodes\RolesAndPermissions\Commands\RolesAndPermissionsCommand;

class RolesAndPermissionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('roles-and-permissions')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_roles-and-permissions_table')
            ->hasCommand(RolesAndPermissionsCommand::class);
    }
}
