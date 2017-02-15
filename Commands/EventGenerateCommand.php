<?php
namespace Pingpong\Modules\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class EventGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:event-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the missing events and listeners based on registration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $provider = $this->laravel->getProvider(
            'Illuminate\Foundation\Support\Providers\EventServiceProvider'
        );
        foreach ($provider->listens() as $event => $listeners) {
            $module = explode('\\', $event);

            if (! Str::contains($event, '\\')) {
                continue;
            }

            $event = $module[3];

            $this->callSilent('module:make-event', ['name' => $event, 'module' => $module[1]]);

            foreach ($listeners as $listener) {
                $listener = preg_replace('/@.+$/', '', $listener);
                $module = explode('\\', $listener);

                $this->callSilent('module:make-listener', ['name' => $module[3], 'module' => $module[1], '--event' => $event]);
            }
        }

        $this->info('Events and listeners generated successfully!');
    }
}
