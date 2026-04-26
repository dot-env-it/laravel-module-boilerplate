<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Console\Command;

class ModuleEventListener extends Command
{
    protected $signature = 'module:event-listener {event} {--module= : The name of the module} {--model=}';

    protected $description = 'Generate a new event and its corresponding listener for a specific module';

    public function handle(): void
    {
        $event  = $this->argument('event');
        $module = $this->option('module');
        $model  = $this->option('model') ?? $module;

        $module_lower = strtolower($module);

        // 1. Standard Laravel Components
        $this->call('module:event', ['name' => $event, '--module' => $module, '--model' => $model]);
        $this->call('module:listener', ['name' => "{$event}Listener", '--module' => $module, '--event' => $event]);

        $this->confirm('Do you want to create a Job for this event?', true)
        && ($jobName = $this->ask("Enter the name of the Job(default: {$event}Job)", "{$event}Job"))
        && $this->call('module:job', ['name' => $jobName, '--module' => $module]);

        // You can add more like 'make:action', 'make:command', etc.
        $this->info("Successfully created event for the {$event} module.");
    }
}
