<?php

namespace Modules\Tax\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TaxServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Tax';

    protected string $nameLower = 'tax';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();

        // Register module database connection
        $dbConfig = config('tax.database');
        $this->app['config']['database.connections.tax'] = $dbConfig;

        $this->registerViews();
        $this->registerObservers();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->registerServices();
    }

    /**
     * Register module services.
     */
    protected function registerServices(): void
    {
        // Tax Repository (Master)
        $this->app->bind(
            \Modules\Tax\Repositories\Tax\ITaxRepository::class,
            \Modules\Tax\Repositories\Tax\TaxRepository::class
        );

        // Tax Service (Master)
        $this->app->bind(
            \Modules\Tax\Services\Tax\ITaxService::class,
            \Modules\Tax\Services\Tax\TaxService::class
        );

        // Tax Group Repository (Config)
        $this->app->bind(
            \Modules\Tax\Repositories\TaxGroup\ITaxGroupRepository::class,
            \Modules\Tax\Repositories\TaxGroup\TaxGroupRepository::class
        );

        // Tax Group Service
        $this->app->bind(
            \Modules\Tax\Services\TaxGroup\ITaxGroupService::class,
            \Modules\Tax\Services\TaxGroup\TaxGroupService::class
        );

        // Tax Assignment Repository
        $this->app->bind(
            \Modules\Tax\Repositories\TaxAssignment\ITaxAssignmentRepository::class,
            \Modules\Tax\Repositories\TaxAssignment\TaxAssignmentRepository::class
        );

        // Tax Rate Repository
        $this->app->bind(
            \Modules\Tax\Repositories\TaxRate\ITaxRateRepository::class,
            \Modules\Tax\Repositories\TaxRate\TaxRateRepository::class
        );

        // Tax Rate Service
        $this->app->bind(
            \Modules\Tax\Services\TaxRate\ITaxRateService::class,
            \Modules\Tax\Services\TaxRate\TaxRateService::class
        );

        // Tax Calculation Service
        $this->app->bind(
            \Modules\Tax\Services\TaxCalculation\ITaxCalculationService::class,
            \Modules\Tax\Services\TaxCalculation\TaxCalculationService::class
        );
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void {}

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            // Load translations from module's resources/lang directory
            $moduleLangPath = module_path($this->name, 'resources/lang');
            if (is_dir($moduleLangPath)) {
                $this->loadTranslationsFrom($moduleLangPath, $this->nameLower);
                $this->loadJsonTranslationsFrom($moduleLangPath);
            }
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower . '.' . $config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace') . '\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
