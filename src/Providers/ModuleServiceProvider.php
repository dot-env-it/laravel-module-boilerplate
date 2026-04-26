<?php

namespace DotEnvIt\ModuleBoilerplate\Providers;

use DotEnvIt\ModuleBoilerplate\Console\Commands\MakeModule;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleAction;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleController;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleDataTable;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleEnum;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleEvent;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleEventListener;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleJob;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleListener;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleModel;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleNotification;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModulePayload;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModulePolicy;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleQuery;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleRequest;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleResource;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleService;
use DotEnvIt\ModuleBoilerplate\Console\Commands\ModuleTest;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MakeModule::class,
        ModuleAction::class,
        ModuleController::class,
        ModuleDataTable::class,
        ModuleEnum::class,
        ModuleEvent::class,
        ModuleEventListener::class,
        ModuleJob::class,
        ModuleListener::class,
        ModuleModel::class,
        ModuleNotification::class,
        ModulePayload::class,
        ModulePolicy::class,
        ModuleQuery::class,
        ModuleRequest::class,
        ModuleResource::class,
        ModuleService::class,
        ModuleTest::class,
    ];

    public function boot()
    {
        if ($this->app->isProduction()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);

            // Publishing stubs so they can be customized in the main app
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/vendor/dot-env-it'),
            ], 'module-boilerplate-stubs');
        }
    }

    public function register()
    {
    }
}
