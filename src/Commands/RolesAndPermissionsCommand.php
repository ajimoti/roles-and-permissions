<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RolesAndPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'roles:install';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Installs the roles and permissions package';

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

        $this->call('make:role', ['name' => 'Role']);
        $this->call('make:permission', ['name' => 'Permission']);

        $this->comment('Package installed successfully.');

        return self::SUCCESS;
    }

    /**
     * Check if a config file already exists
     *
     * @param string $fileName
     * @return bool
     */
    private function configExists(string $fileName): bool
    {
        return File::exists(config_path($fileName));
    }

    /**
     * Verify if the user wants to overwrite the config file
     *
     * @return bool
     */
    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    /**
     * Publish the configuration file
     *
     * @param bool $overwrite
     * @return void
     */
    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Tarzancodes\RolesAndPermissions\RolesAndPermissionsServiceProvider",
            '--tag' => "roles-and-permissions-config",
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
