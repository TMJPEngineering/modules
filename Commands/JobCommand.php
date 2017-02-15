<?php
namespace Pingpong\Modules\Commands;

use Illuminate\Support\Str;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Pingpong\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;

class JobCommand extends GeneratorCommand
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
    protected $name = 'module:make-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new job for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of job will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/job.stub', [
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

        $jobPath = $this->laravel['modules']->config('paths.generator.job');

        return $path . $jobPath . '/' . $this->getJobName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getJobName()
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
        return 'Jobs';
    }
}
