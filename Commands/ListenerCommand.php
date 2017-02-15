<?php
namespace Pingpong\Modules\Commands;

use Pingpong\Support\Stub;
use Pingpong\Modules\Traits\ModuleCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ListenerCommand extends GeneratorCommand
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
    protected $name = 'module:make-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new listener for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of listener will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['event', null, InputOption::VALUE_REQUIRED, 'The event class being listened for.']
        ];
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/listener.stub', [
            'CLASS' => $this->getClass(),
            'MODULE' => $this->getModuleName(),
            'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
            'EVENT_NAME' => $this->option('event')
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $listenerPath = $this->laravel['modules']->config('paths.generator.listener');

        return $path . $listenerPath . '/' . $this->getListenerName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getListenerName()
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
        return 'Listener';
    }
}
