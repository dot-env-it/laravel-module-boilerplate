<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Support\Str;

class ModuleController extends BaseModuleGenerator
{
    protected $name = 'module:controller';
    protected $type = 'Controller';

    protected function getStub(): string
    {
        $action = strtolower($this->option('action'));

        return file_exists($this->getStubPath("controller.invokable.{$action}.stub"))
            ? $this->getStubPath("controller.invokable.{$action}.stub")
            : $this->getStubPath('controller.invokable.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR .
            'Modules' . DIRECTORY_SEPARATOR .
            Str::title($this->option('module')) . DIRECTORY_SEPARATOR .
            'Http' . DIRECTORY_SEPARATOR .
            Str::plural($this->type) . DIRECTORY_SEPARATOR .
            'Api' . DIRECTORY_SEPARATOR . 'v1';
    }
}
