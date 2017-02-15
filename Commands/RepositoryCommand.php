<?php
namespace Pingpong\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Pingpong\Support\Stub;
use Pingpong\Modules\Traits\ModuleCommandTrait;

class RepositoryCommand extends Command
{
    use ModuleCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new repository.';

    /**
     * The constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $name = $this->argument('name');
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        if ( ! $this->filesystem->exists(app_path('Interfaces\BaseEloquent.php'))) {
            $this->filesystem->makeDirectory(app_path('Interfaces'), 0755, true);
            $this->filesystem->put(app_path('Interfaces\BaseEloquent.php'), $this->getStubContent('base-interface'));
            $this->info("Created : " . app_path('Interfaces\BaseEloquent.php'));
        }

        if ( ! $this->filesystem->exists(app_path('Repositories\BaseEloquentRepository.php'))) {
            $this->filesystem->makeDirectory(app_path('Repositories'), 0755, true);
            $this->filesystem->put(app_path('Repositories\BaseEloquentRepository.php'), $this->getStubContent('base-eloquent'));
            $this->info("Created : " . app_path('Repositories\BaseEloquentRepository.php'));
        }

        $interfacePath = $this->laravel['modules']->config('paths.generator.interface');
        $interfaceFile = $path . "$interfacePath/" . $name . '.php';

        if ( ! $this->filesystem->exists($interfaceFile)) {
            $this->filesystem->put($interfaceFile, $this->getStubContent('interface', [
                'MODULE' => $this->getModuleName(),
                'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
            ]));
            $this->info("Created : {$interfaceFile}");
        } else {
            $this->error("File : {$interfaceFile} already exists.");
        }

        $repositoryPath = $this->laravel['modules']->config('paths.generator.repository');
        $repositoryFile = $path . "$repositoryPath/Eloquent" . $name . '.php';

        if ( ! $this->filesystem->exists($repositoryFile)) {
            $this->filesystem->put($repositoryFile, $this->getStubContent('repository', [
                'MODULE' => $this->getModuleName(),
                'MODULE_NAMESPACE' => $this->laravel['modules']->config('namespace'),
            ]));
            $this->info("Created : {$repositoryFile}");
        } else {
            $this->error("File : {$repositoryFile} already exists.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of test will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    public function getStubContent($stub, $replaces = [])
    {
        return (new Stub('/'.$stub.'.stub', $replaces))->render();
    }
}
