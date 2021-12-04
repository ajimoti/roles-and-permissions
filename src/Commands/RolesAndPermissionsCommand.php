<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\Command;

class RolesAndPermissionsCommand extends Command
{
    public $signature = 'roles-and-permissions';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
