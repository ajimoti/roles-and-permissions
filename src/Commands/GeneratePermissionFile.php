<?php

namespace Tarzancodes\RolesAndPermissions\Commands;

use Illuminate\Console\GeneratorCommand;

class GeneratePermissionFile extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Generate an enum file for permissions';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Permission';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../Stubs/Permission.php.stub';
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

        $this->info('Permission file generated successfully!');

        return self::SUCCESS;
    }
}
