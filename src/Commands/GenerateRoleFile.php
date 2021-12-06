<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateRoleFile extends GeneratorCommand
{
    protected $name = 'roles:generate';

    public $description = 'Generate an enum file for roles';

    protected $type = 'Role';

    protected function getStub()
    {
        return __DIR__ . '/../Stubs/Role.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Enums';
    }

    public function handle(): int
    {
        $this->info('Generating Role file...');

        parent::handle();

        $this->info('Files generated successfully!');

        return self::SUCCESS;
    }
}
