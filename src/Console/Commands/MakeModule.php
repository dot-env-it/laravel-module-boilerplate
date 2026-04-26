<?php

namespace DotEnvIt\ModuleBoilerplate\Console\Commands;

use App\Enums\Role;
use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name} {--roles=*} {--module=} {--model=} {--js-path=resources/assets/extended/js/custom : Custom destination for JS files}';
    protected $description = 'Generate a full module structure';

    public function handle(): void
    {

        // Prevent execution in production even if called via artisan
        if (app()->isProduction()) {
            $this->error('This generator is strictly for local development!');
            return 1;
        }
        
        $name = $this->argument('name');
        $module = $this->option('module') ?? $name;
        $model = $this->option('model') ?? $name;

        $params = ['--module' => $module, '--sub' => $name];
        $actions = ['Index', 'Show', 'Store', 'Update', 'Destroy'];
        $migrationPath = "database/migrations/{$module}";

        // 1. Standard Laravel Components
        $this->call('module:model', ['name' => $model] + $params);

        // Ensure the sub directory exists (required by the make:migration command)
        if (!is_dir(base_path($migrationPath))) {
            mkdir(base_path($migrationPath), 0755, true);
        }

        // Call the migration generator with the custom path
        $this->call('make:migration', [
            'name' => "create_" . Str::of($name)->plural()->snake()->lower()->toString()
                . "_table",
            '--path' => $migrationPath,
        ]);

        $this->call('make:factory', [
            'name' => "{$name}Factory",
            '--model' => "App\\Modules\\{$module}\\Models\\v1\\{$model}",
        ]);
        $this->call('make:seeder', ['name' => "{$name}Seeder"]);

        // 2. Generate Action-Specific Components
        foreach ($actions as $action) {
            // Create the Action class (e.g., app/Modules/User/Actions/StoreUserAction.php)
            if (in_array($action, ['Store', 'Update', 'Destroy'])) {
                $this->call('module:action', [
                    'name' => "{$action}{$name}Action",
                    '--module' => $module,
                    '--model' => $model,
                    '--action' => $action,
                ]);

                // Create Request for each action
                $this->call('module:request', [
                    'name' => "{$action}{$name}Request",
                    '--module' => $module,
                    '--model' => $model,
                ]);
            }


            // Create Invokable Controller for each action
            $this->call('module:controller', [
                'name' => "{$action}{$name}Controller",
                '--module' => $module,
                '--model' => $model,
                '--action' => $action,
            ]);
        }

        $this->call('module:resource', ['name' => "{$name}Resource"] + $params);
        $this->call('module:resource', ['name' => "{$name}Collection", '--collection' => true] + $params);

        // 2. Custom Components
        $this->call('module:data-table', ['name' => "{$name}DataTable"] + $params);
        $this->call('module:service', ['name' => "{$name}Service"] + $params);
        $this->call('module:query', ['name' => "{$name}Query"] + $params);
        $this->call('module:payload', ['name' => "{$name}Payload"] + $params);
        $this->call('module:test', ['name' => "{$name}Test"] + $params);

        $this->makeViews($name);
        $this->makeJs($name);
        $this->appendRoutes($name, $module);
        
        if ($this->hasPermissionPlugin()) {
            $this->createPermissions($name);
        }

        // You can add more like 'make:action', 'make:command', etc.
        $this->info("Successfully created all components for the {$name} module.");
    }

    private function makeJs(string $name): void
    {
        $jsPath = $this->option('js-path');

        $name = str($name)->kebab()->toString();

        $sep = DIRECTORY_SEPARATOR;

        // Copy the view stub to the appropriate location
        $stubViewDir = $this->getStubPath("js{$sep}_module");
        // resources/assets/extended/js/custom/modules/company
        $targetJsDir = base_path("{$jsPath}{$sep}modules{$sep}" . $name);

        if (!is_dir($targetJsDir)) {
            mkdir($targetJsDir, 0755, true);
        }

        foreach (glob("{$stubViewDir}/**/*.stub") as $stubFile) {
            // 1. Get the relative path from the stub root (e.g., "list/_action-menu.stub")
            $relativePath = trim(explode('_module', $stubFile)[1], "\\/");

            // 2. Determine the target filename with the correct extension
            $targetRelativePath = str_replace('.stub', '.js', $relativePath);
            $targetFile = $targetJsDir . DIRECTORY_SEPARATOR . $targetRelativePath;

            // 3. Ensure the nested target directory exists
            $targetSubDir = dirname($targetFile);
            if (!is_dir($targetSubDir)) {
                mkdir($targetSubDir, 0755, true);
            }

            if (file_exists($targetFile)) {
                $this->warn("Javascript file already exists: {$targetFile}. Skipping.");
                continue;
            }

            // replace placeholders in the stub file
            $stubContent = file_get_contents($stubFile);
            $stubContent = str_replace(
                ['{{ sub }}', '{{ module_lower }}', '{{ module_plural }}',],
                [Str::headline($name), $name, Str::plural($name),],
                $stubContent
            );

            file_put_contents($targetFile, $stubContent);
        }

        $this->info("Javascript files created for module '{$name}' in {$targetJsDir}");
    }

    private function makeViews(string $name): void
    {
        $name = str($name)->kebab()->toString();

        $sep = DIRECTORY_SEPARATOR;

        // Copy the view stub to the appropriate location
        $stubViewDir = $this->getStubPath("views{$sep}_module");
        $targetViewDir = base_path("resources{$sep}views{$sep}modules{$sep}" . $name);

        if (!is_dir($targetViewDir)) {
            mkdir($targetViewDir, 0755, true);
        }

        foreach (glob("{$stubViewDir}/**/*.stub") as $stubFile) {
            // 1. Get the relative path from the stub root (e.g., "list/_action-menu.stub")
            $relativePath = trim(explode('_module', $stubFile)[1], "\\/");

            // 2. Determine the target filename with the correct extension
            $targetRelativePath = str_replace('.stub', '.blade.php', $relativePath);
            $targetFile = $targetViewDir . DIRECTORY_SEPARATOR . $targetRelativePath;

            // 3. Ensure the nested target directory exists
            $targetSubDir = dirname($targetFile);
            if (!is_dir($targetSubDir)) {
                mkdir($targetSubDir, 0755, true);
            }

            if (file_exists($targetFile)) {
                $this->warn("View file already exists: {$targetFile}. Skipping.");
                continue;
            }

            // replace placeholders in the stub file
            $stubContent = file_get_contents($stubFile);
            $stubContent = str_replace('{{ module }}', Str::headline($name), $stubContent);
            $stubContent = str_replace('{{ module_lower }}', $name, $stubContent);
            $stubContent = str_replace('{{ module_plural }}', Str::plural($name), $stubContent);

            file_put_contents($targetFile, $stubContent);
        }

        $this->info("View files created for module '{$name}' in {$targetViewDir}");
    }

    private function appendRoutes(string $name, string $module): void
    {
        $routeFile = base_path('routes/modules.php');
        $routeStub = $this->getStubPath('routes.stub');
        if ($name !== $module) {
            $routeStub = $this->getStubPath('routes.module.stub');
        }

        if (file_exists($routeStub)) {
            $stubContent = file_get_contents($routeStub);

            $stubContent = str_replace('{{ name }}', $name, $stubContent);
            $stubContent = str_replace('{{ name_plural }}', Str::of($name)->plural()->lower()->snake('-'), $stubContent);
            $stubContent = str_replace('{{ name_lower }}', Str::lower($name), $stubContent);
            $stubContent = str_replace('{{ module }}', $module, $stubContent);
            $stubContent = str_replace('{{ module_plural }}', Str::of($module)->plural()->lower()->snake('-'), $stubContent);
            $stubContent = str_replace('{{ module_lower }}', Str::lower($module), $stubContent);

            // ignore if the route already exists
            if (
                file_exists($routeFile)
                && str_contains(
                    file_get_contents($routeFile),
                    "App\\Modules\\{$module}\\Http\\Controllers\\v1\\Index{$name}Controller"
                )
            ) {
                $this->warn("Routes for module '{$name}' already exist in {$routeFile}. Skipping route appending.");
                return;
            }

            file_put_contents($routeFile, PHP_EOL . $stubContent, FILE_APPEND);

            $this->info("Routes for module '{$name}' appended to {$routeFile}");
        } else {
            $this->error("Route stub not found at {$routeStub}");
        }
    }

    private function createPermissions($name): void
    {
        $permissions = $this->crudActions($name);

        foreach ($permissions as $permission) {
            $this->call('permission:create-permission', ['name' => $permission]);

            $superAdmin = \Spatie\Permission\Models\Role::findByName(Role::SUPER_ADMIN->value);

            $superAdmin->givePermissionTo($permission);

            $developer = \Spatie\Permission\Models\Role::findByName(Role::DEVELOPER->value);

            $developer->givePermissionTo($permission);

            $roles = $this->option('roles');
            foreach ($roles as $role) {

                if (\Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                    $roleModel = \Spatie\Permission\Models\Role::findByName($role);
                    $roleModel->givePermissionTo($permission);
                } else {
                    $this->warn("Role '{$role}' does not exist. Skipping permission assignment for this role.");
                }
            }
        }
    }

    private function crudActions($name): array
    {
        $name = Str::lower($name);

        $actions = [];
        // list of permission actions
        $crud = ['create', 'view', 'update', 'delete'];

        foreach ($crud as $value) {
            $actions[] = $value . ' ' . $name;
        }

        return $actions;
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

    /**
     * Check if Spatie Laravel Permission is active.
     */
    protected function hasPermissionPlugin(): bool
    {
        return InstalledVersions::isInstalled('spatie/laravel-permission');
    }
}
