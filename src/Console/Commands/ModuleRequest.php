<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Support\Str;

class ModuleRequest extends BaseModuleGenerator
{
    protected $name = 'module:request';
    protected $type = 'Request';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR .
            'Modules' . DIRECTORY_SEPARATOR .
            Str::title($this->option('module')) . DIRECTORY_SEPARATOR .
            'Http' . DIRECTORY_SEPARATOR .
            Str::plural($this->type) . DIRECTORY_SEPARATOR .
            'v1';
    }
}

