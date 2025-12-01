<?php

namespace Modules\Customer\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CustomerServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Customer';

    protected string $nameLower = 'customer';

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
        $dbConfig = config('customer.database');
        $this->app['config']['database.connections.customer'] = $dbConfig;

        $this->registerViews();
        $this->registerMiddleware();
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

        // Register customer authentication middleware
        $router->aliasMiddleware('customer.auth', \Modules\Customer\Http\Middleware\CustomerAuthMiddleware::class);

        // Register customer permission middleware
        $router->aliasMiddleware('customer.permission', \Modules\Customer\Http\Middleware\CustomerPermissionMiddleware::class);
    }

    /**
     * Register repository bindings
     */
    protected function registerRepositories(): void
    {
        // Profile Repository
        $this->app->bind(
            \Modules\Customer\Repositories\Profile\IProfileRepository::class,
            \Modules\Customer\Repositories\Profile\ProfileRepository::class
        );

        // Document Repository
        $this->app->bind(
            \Modules\Customer\Repositories\Document\IDocumentRepository::class,
            \Modules\Customer\Repositories\Document\DocumentRepository::class
        );

        // Address Repository
        $this->app->bind(
            \Modules\Customer\Repositories\Address\IAddressRepository::class,
            \Modules\Customer\Repositories\Address\AddressRepository::class
        );
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // Profile Service
        $this->app->bind(
            \Modules\Customer\Services\Profile\IProfileService::class,
            \Modules\Customer\Services\Profile\ProfileService::class
        );

        // File Upload Service
        $this->app->bind(
            \Modules\Customer\Services\FileUpload\IFileUploadService::class,
            \Modules\Customer\Services\FileUpload\FileUploadService::class
        );

        // Document Service
        $this->app->bind(
            \Modules\Customer\Services\Document\IDocumentService::class,
            \Modules\Customer\Services\Document\DocumentService::class
        );

        // Add other service bindings here as needed
        // Address Service
        // $this->app->bind(
        //     \Modules\Customer\Services\Address\IAddressService::class,
        //     \Modules\Customer\Services\Address\AddressService::class
        // );

        // Address Service
        // $this->app->bind(
        //     \Modules\Customer\Services\Address\IAddressService::class,
        //     \Modules\Customer\Services\Address\AddressService::class
        // );
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        // Add shared repository bindings here if needed
        // $this->app->bind(
        //     \App\Shared\Profile\Services\IProfileService::class,
        //     \Modules\Customer\Services\Profile\ProfileService::class
        // );
    }

    /**
     * Register shared service bindings
     */
    protected function registerSharedServices(): void
    {
        // Add shared service bindings here if needed
        // $this->app->bind(
        //     \App\Shared\Profile\Services\IProfileService::class,
        //     \Modules\Customer\Services\Profile\ProfileService::class
        // );
    }

    /**
     * Register cache key manager binding
     */
    protected function registerCacheKeyManager(): void
    {
        $this->app->bind(
            \Modules\Customer\Cache\KeyManager\IKeyManager::class,
            \Modules\Customer\Cache\KeyManager\KeyManager::class
        );
    }

    /**
     * Register policy bindings
     */
    protected function registerPolicies(): void
    {
        // Profile Ownership Policy
        $this->app->bind(
            \Modules\Customer\Policies\ProfileOwnership\IProfileOwnershipPolicy::class,
            \Modules\Customer\Policies\ProfileOwnership\ProfileOwnershipPolicy::class
        );

        // Document Ownership Policy
        $this->app->bind(
            \Modules\Customer\Policies\DocumentOwnership\IDocumentOwnershipPolicy::class,
            \Modules\Customer\Policies\DocumentOwnership\DocumentOwnershipPolicy::class
        );

        // Document Status Policy
        $this->app->bind(
            \Modules\Customer\Policies\DocumentStatus\IDocumentStatusPolicy::class,
            \Modules\Customer\Policies\DocumentStatus\DocumentStatusPolicy::class
        );

        // Address Management Policy
        $this->app->bind(
            \Modules\Customer\Policies\AddressManagement\IAddressManagementPolicy::class,
            \Modules\Customer\Policies\AddressManagement\AddressManagementPolicy::class
        );

        // Document Upload Policy
        $this->app->bind(
            \Modules\Customer\Policies\DocumentUpload\IDocumentUploadPolicy::class,
            \Modules\Customer\Policies\DocumentUpload\DocumentUploadPolicy::class
        );
    }

    /**
     * Register model observers
     */
    protected function registerObservers(): void
    {
        // Register model observers for cache management
        \Modules\Customer\Models\Profile::observe(\Modules\Customer\Observers\ProfileObserver::class);
        \Modules\Customer\Models\Document::observe(\Modules\Customer\Observers\DocumentObserver::class);
        \Modules\Customer\Models\Address::observe(\Modules\Customer\Observers\AddressObserver::class);
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
