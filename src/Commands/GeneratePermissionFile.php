<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class GeneratePermissionFile extends GeneratorCommand
{
    protected $name = 'roles:generate-permission';

    public $description = 'Generate an enum file for permissions';

    protected $type = 'Permission';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/Permission.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Enums';
    }

    public function handle(): int
    {
        $this->info('Generating file...');

        parent::handle();

        $this->info('File generated successfully!');

        return self::SUCCESS;
    }
}
