<?php

namespace Ajimoti\RolesAndPermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class GenerateRoleFile extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:role';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Generate an enum file for roles';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Role';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../Stubs/Role.php.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Enums';
    }

    public function handle(): int
    {
        parent::handle();

        $this->info('Role file generated successfully!');

        return self::SUCCESS;
    }
}
