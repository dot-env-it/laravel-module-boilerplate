<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseModuleGenerator extends GeneratorCommand
{
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR
            . 'Modules' . DIRECTORY_SEPARATOR
            . Str::title($this->option('module')) . DIRECTORY_SEPARATOR
            . Str::plural($this->type) . DIRECTORY_SEPARATOR
            . 'v1';
    }

    protected function buildClass($name): array|string
    {
        $stub = parent::buildClass($name);

        $module = $this->option('module');

        if (! $module) {
            $this->error('The --module option is required.');

            exit(1);
        }

        $action        = $this->option('action') ?? '';
        $event         = $this->option('event')  ?? '';
        $model         = $this->option('model');
        $sub           = $this->option('sub') ?? $model ?? '';
        $module_lower  = Str::lower($module);
        $model_lower   = Str::lower($model);
        $sub_lower     = Str::lower($sub);
        $sub_plural    = Str::plural($sub);
        $module_plural = Str::plural($module);

        return str_replace(
            [
                '{{ module }}', '{{ action }}', '{{ module_lower }}', '{{ module_plural }}', '{{ event }}',
                '{{ sub }}', '{{ sub_lower }}', '{{ sub_plural }}', '{{ model }}', '{{ model_lower }}',
            ],
            [
                $module, $action, $module_lower, $module_plural, $event,
                $sub, $sub_lower, $sub_plural, $model, $model_lower,
            ],
            $stub
        );
    }

    protected function getOptions(): array
    {
        return [
            ['module', null, InputOption::VALUE_REQUIRED, 'The name of the module'],
            ['sub', null, InputOption::VALUE_REQUIRED, 'The name of the sub module'],
            ['action', null, InputOption::VALUE_OPTIONAL, 'The specific action name (Store, Update, etc.)'],
            ['event', null, InputOption::VALUE_OPTIONAL, 'The event name for listeners'],
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model name'],
        ];
    }

    protected function getStub(): string
    {
        $stub_name = Str::lower($this->type);

        return $this->getStubPath("{$stub_name}.stub");
    }

    protected function getStubPath($stubName)
    {
        // 1. Check for published version in the project root
        $publishedPath = base_path("stubs/vendor/dot-env-it/{$stubName}");

        if (File::exists($publishedPath)) {
            return $publishedPath;
        }

        // 2. Fallback to the internal package stub
        return __DIR__ . "/../../stubs/{$stubName}";
    }
}
