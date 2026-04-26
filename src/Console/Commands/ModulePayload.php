<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Support\Str;

class ModulePayload extends BaseModuleGenerator
{
    protected $name = 'module:payload';

    protected $type = 'Payload';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR
            . 'Modules' . DIRECTORY_SEPARATOR
            . Str::title($this->option('module')) . DIRECTORY_SEPARATOR
            . 'Http' . DIRECTORY_SEPARATOR
            . Str::plural($this->type) . DIRECTORY_SEPARATOR
            . 'v1';
    }
}
