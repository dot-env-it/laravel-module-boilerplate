<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleResource extends BaseModuleGenerator
{
    protected $name = 'module:resource';

    protected $type = 'Resource';

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . DIRECTORY_SEPARATOR
            . 'Modules' . DIRECTORY_SEPARATOR
            . Str::title($this->option('module')) . DIRECTORY_SEPARATOR
            . 'Http' . DIRECTORY_SEPARATOR
            . Str::plural($this->type) . DIRECTORY_SEPARATOR
            . 'v1';
    }

    protected function getStub(): string
    {
        return $this->option('collection')
            ? $this->getStubPath('resource-collection.stub')
            : $this->getStubPath('resource.stub');
    }

    protected function getOptions(): array
    {
        return array_merge(parent::getOptions(), [
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
        ]);
    }
}
