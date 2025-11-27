<?php

namespace Modules\Profile\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProfileServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Profile';

    protected string $nameLower = 'profile';

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
        $dbConfig = config('profile.database');
        $this->app['config']['database.connections.profile'] = $dbConfig;

        $this->registerViews();
        $this->registerMiddleware();

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

        // Register repositories
        $this->registerRepositories();

        // Register services
        $this->registerServices();

        // Register shared services
        $this->registerSharedServices();

        // Register shared repositories
        $this->registerSharedRepositories();
    }

    /**
     * Register repository bindings
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \Modules\Profile\Repositories\Profile\IProfileRepository::class,
            \Modules\Profile\Repositories\Profile\ProfileRepository::class
        );

        $this->app->bind(
            \Modules\Profile\Repositories\Address\IAddressRepository::class,
            \Modules\Profile\Repositories\Address\AddressRepository::class
        );

        $this->app->bind(
            \Modules\Profile\Repositories\Bank\IBankRepository::class,
            \Modules\Profile\Repositories\Bank\BankRepository::class
        );

        $this->app->bind(
            \Modules\Profile\Repositories\Driver\IDriverRepository::class,
            \Modules\Profile\Repositories\Driver\DriverRepository::class
        );

        $this->app->bind(
            \Modules\Profile\Repositories\Merchant\IMerchantRepository::class,
            \Modules\Profile\Repositories\Merchant\MerchantRepository::class
        );

        $this->app->bind(
            \Modules\Profile\Repositories\Customer\ICustomerDocumentRepository::class,
            \Modules\Profile\Repositories\Customer\CustomerDocumentRepository::class
        );

        // Register shared repositories
        $this->registerSharedRepositories();
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // Cache Key Manager
        $this->app->bind(
            \Modules\Profile\Cache\KeyManager\IKeyManager::class,
            \Modules\Profile\Cache\KeyManager\KeyManager::class
        );

        // File Upload Service
        $this->app->bind(
            \Modules\Profile\Services\FileUpload\IFileUploadService::class,
            \Modules\Profile\Services\FileUpload\FileUploadService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Address\IAddressService::class,
            \Modules\Profile\Services\Address\AddressService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Bank\IBankService::class,
            \Modules\Profile\Services\Bank\BankService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Driver\IDriverService::class,
            \Modules\Profile\Services\Driver\DriverService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Merchant\IMerchantService::class,
            \Modules\Profile\Services\Merchant\MerchantService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Customer\ICustomerService::class,
            \Modules\Profile\Services\Customer\CustomerService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Customer\ICustomerDocumentService::class,
            \Modules\Profile\Services\Customer\CustomerDocumentService::class
        );

        $this->app->bind(
            \Modules\Profile\Services\Customer\ICustomerAddressService::class,
            \Modules\Profile\Services\Customer\CustomerAddressService::class
        );
    }

    /**
     * Register shared service bindings
     */
    protected function registerSharedServices(): void
    {
        // Profile module doesn't have shared services yet
        // This can be expanded when shared interfaces are created
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        // Profile module doesn't have shared repositories yet
        // This can be expanded when shared interfaces are created
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Authentication middleware (JWT validation only)
        $router->aliasMiddleware('profile.auth', \Modules\Profile\Http\Middleware\AuthenticationMiddleware::class);

        // Authorization middleware (JWT + permission check)
        $router->aliasMiddleware('profile.can', \Modules\Profile\Http\Middleware\AuthorizationMiddleware::class);
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
