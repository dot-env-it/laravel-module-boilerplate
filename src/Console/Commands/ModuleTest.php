<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use Illuminate\Support\Str;

class ModuleTest extends BaseModuleGenerator
{
    protected $name = 'module:test';
    protected $type = 'Test';

    protected function getPath($name): string
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        // Change 'custom_tests' to your preferred folder name
        return base_path('tests' . DIRECTORY_SEPARATOR . 'Feature' . DIRECTORY_SEPARATOR .
                Str::title($this->option('module')) . DIRECTORY_SEPARATOR) . basename($name) . '.php';
    }
}
