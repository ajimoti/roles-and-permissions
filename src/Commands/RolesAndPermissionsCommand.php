<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RolesAndPermissionsCommand extends Command
{
    public $signature = 'roles:install';

    public $description = 'Install the roles and permissions package';

    public function handle(): int
    {
        $this->info('Installing Tarzancodes roles and permissions...');

        $this->info('Publishing configuration...');

        // Publish config file
        if ($this->configExists('roles-and-permissions.php')) {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration(true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        } else {
            $this->publishConfiguration();
            $this->info('Published configuration');
        }

        $this->call('roles:generate', ['name' => 'Role']);
        $this->call('roles:generate-permission', ['name' => 'Permission']);

        $this->comment('Package installed successfully.');

        return self::SUCCESS;
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Tarzancodes\RolesAndPermissions\RolesAndPermissionsServiceProvider",
            '--tag' => "roles-and-permissions-config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

       $this->call('vendor:publish', $params);
    }
}
