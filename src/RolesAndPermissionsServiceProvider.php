<?php

namespace Tarzancodes\RolesAndPermissions;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tarzancodes\RolesAndPermissions\Commands\GeneratePermissionFile;
use Tarzancodes\RolesAndPermissions\Commands\GenerateRoleFile;
use Tarzancodes\RolesAndPermissions\Commands\RolesAndPermissionsCommand;

class RolesAndPermissionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('roles-and-permissions')
            ->hasConfigFile()
            ->hasCommands([RolesAndPermissionsCommand::class, GenerateRoleFile::class, GeneratePermissionFile::class]);
    }
}
