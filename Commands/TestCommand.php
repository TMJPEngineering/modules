<?php
namespace Pingpong\Modules\Commands;

use Illuminate\Support\Str;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Pingpong\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;

class TestCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new test for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of policy will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/test.stub', [
            'NAME' => $this->getTestName()
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $testPath = $this->laravel['modules']->config('paths.generator.test');

        return $path . $testPath . '/' . $this->getTestName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getTestName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace()
    {
        return 'Tests';
    }
}
