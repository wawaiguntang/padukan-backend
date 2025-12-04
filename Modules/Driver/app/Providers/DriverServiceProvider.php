<?php

namespace Modules\Driver\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DriverServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Driver';

    protected string $nameLower = 'driver';

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
        $dbConfig = config('driver.database');
        $this->app['config']['database.connections.driver'] = $dbConfig;

        $this->registerViews();
        $this->registerMiddleware();
        $this->registerFactories();
        $this->registerObservers();

        // Only load migrations if not in testing environment
        if (app()->environment() !== 'testing') {
            $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // Register cache key manager
        $this->registerCacheKeyManager();

        // Register repositories
        $this->registerRepositories();

        // Register services
        $this->registerServices();

        // Register policies
        $this->registerPolicies();

        // Register shared repositories
        $this->registerSharedRepositories();

        // Register shared services
        $this->registerSharedServices();
    }

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
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Register driver authentication middleware
        $router->aliasMiddleware('driver.auth', \Modules\Driver\Http\Middleware\DriverAuthenticationMiddleware::class);

        // Register driver permission middleware
        $router->aliasMiddleware('driver.permission', \Modules\Driver\Http\Middleware\DriverPermissionMiddleware::class);
    }

    /**
     * Register factories.
     */
    protected function registerFactories(): void
    {
        if ($this->app->environment('testing')) {
            $this->loadFactoriesFrom(module_path($this->name, 'database/factories'));
        }
    }

    /**
     * Register repository bindings
     */
    protected function registerRepositories(): void
    {
        // Profile Repository
        $this->app->bind(
            \Modules\Driver\Repositories\Profile\IProfileRepository::class,
            \Modules\Driver\Repositories\Profile\ProfileRepository::class
        );

        // Document Repository
        $this->app->bind(
            \Modules\Driver\Repositories\Document\IDocumentRepository::class,
            \Modules\Driver\Repositories\Document\DocumentRepository::class
        );

        // Vehicle Repository
        $this->app->bind(
            \Modules\Driver\Repositories\Vehicle\IVehicleRepository::class,
            \Modules\Driver\Repositories\Vehicle\VehicleRepository::class
        );

        // Driver Status Repository
        $this->app->bind(
            \Modules\Driver\Repositories\DriverStatus\IDriverStatusRepository::class,
            \Modules\Driver\Repositories\DriverStatus\DriverStatusRepository::class
        );
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // Profile Service
        $this->app->bind(
            \Modules\Driver\Services\Profile\IProfileService::class,
            function ($app) {
                return new \Modules\Driver\Services\Profile\ProfileService(
                    $app->make(\Modules\Driver\Repositories\Profile\IProfileRepository::class),
                    $app->make(\Modules\Driver\Repositories\Document\IDocumentRepository::class),
                    $app->make(\Modules\Driver\Services\Document\IDocumentService::class),
                    $app->make(\Modules\Driver\Services\FileUpload\IFileUploadService::class),
                    $app->make(\Modules\Driver\Policies\ProfileOwnership\IProfileOwnershipPolicy::class),
                    $app->make(\App\Shared\Setting\Services\ISettingService::class)
                );
            }
        );

        // File Upload Service
        $this->app->bind(
            \Modules\Driver\Services\FileUpload\IFileUploadService::class,
            \Modules\Driver\Services\FileUpload\FileUploadService::class
        );

        // Document Service
        $this->app->bind(
            \Modules\Driver\Services\Document\IDocumentService::class,
            \Modules\Driver\Services\Document\DocumentService::class
        );

        // Vehicle Service
        $this->app->bind(
            \Modules\Driver\Services\Vehicle\IVehicleService::class,
            \Modules\Driver\Services\Vehicle\VehicleService::class
        );

        // Driver Status Service
        $this->app->bind(
            \Modules\Driver\Services\DriverStatus\IDriverStatusService::class,
            \Modules\Driver\Services\DriverStatus\DriverStatusService::class
        );
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        // Add shared repository bindings here if needed
    }

    /**
     * Register shared service bindings
     */
    protected function registerSharedServices(): void
    {
        // Add shared service bindings here if needed
    }

    /**
     * Register cache key manager binding
     */
    protected function registerCacheKeyManager(): void
    {
        $this->app->bind(
            \Modules\Driver\Cache\KeyManager\IKeyManager::class,
            \Modules\Driver\Cache\KeyManager\KeyManager::class
        );
    }

    /**
     * Register policy bindings
     */
    protected function registerPolicies(): void
    {
        // Profile Ownership Policy
        $this->app->bind(
            \Modules\Driver\Policies\ProfileOwnership\IProfileOwnershipPolicy::class,
            \Modules\Driver\Policies\ProfileOwnership\ProfileOwnershipPolicy::class
        );

        // Document Ownership Policy
        $this->app->bind(
            \Modules\Driver\Policies\DocumentOwnership\IDocumentOwnershipPolicy::class,
            \Modules\Driver\Policies\DocumentOwnership\DocumentOwnershipPolicy::class
        );

        // Vehicle Management Policy
        $this->app->bind(
            \Modules\Driver\Policies\VehicleManagement\IVehicleManagementPolicy::class,
            \Modules\Driver\Policies\VehicleManagement\VehicleManagementPolicy::class
        );

        // Driver Status Policy
        $this->app->bind(
            \Modules\Driver\Policies\DriverStatus\IDriverStatusPolicy::class,
            \Modules\Driver\Policies\DriverStatus\DriverStatusPolicy::class
        );

        // Service Validation Policy
        $this->app->bind(
            \Modules\Driver\Policies\ServiceValidation\IServiceValidationPolicy::class,
            \Modules\Driver\Policies\ServiceValidation\ServiceValidationPolicy::class
        );
    }

    /**
     * Register model observers
     */
    protected function registerObservers(): void
    {
        // Register model observers for cache management
        \Modules\Driver\Models\Profile::observe(\Modules\Driver\Observers\ProfileObserver::class);
        \Modules\Driver\Models\Vehicle::observe(\Modules\Driver\Observers\VehicleObserver::class);
        \Modules\Driver\Models\DriverAvailabilityStatus::observe(\Modules\Driver\Observers\DriverStatusObserver::class);
        \Modules\Driver\Models\Document::observe(\Modules\Driver\Observers\DocumentObserver::class);
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
