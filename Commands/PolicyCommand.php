<?php
namespace Illuminate\Foundation\Console;

use Illuminate\Support\Str;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Pingpong\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;

class PolicyCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'policy';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new policy for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['policy', InputArgument::REQUIRED, 'The name of policy will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/policy.stub', [
            'CLASS' => $this->getClass(),
            'MODULE' => $this->getModuleName(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $controllerPath = $this->laravel['modules']->config('paths.generator.policy');

        return $path . $controllerPath . '/' . $this->getModelName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getModelName()
    {
        return Str::studly($this->argument('model'));
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace()
    {
        return 'Policies';
    }
}
