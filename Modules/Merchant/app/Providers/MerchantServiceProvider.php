<?php

namespace Modules\Merchant\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MerchantServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Merchant';

    protected string $nameLower = 'merchant';

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
        $dbConfig = config('merchant.database');
        $this->app['config']['database.connections.merchant'] = $dbConfig;

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
        // Profile Services
        $this->app->bind(
            \Modules\Merchant\Repositories\Profile\IProfileRepository::class,
            \Modules\Merchant\Repositories\Profile\ProfileRepository::class
        );

        $this->app->bind(
            \Modules\Merchant\Services\Profile\IProfileService::class,
            \Modules\Merchant\Services\Profile\ProfileService::class
        );

        // Merchant Services
        $this->app->bind(
            \Modules\Merchant\Repositories\Merchant\IMerchantRepository::class,
            \Modules\Merchant\Repositories\Merchant\MerchantRepository::class
        );

        $this->app->bind(
            \Modules\Merchant\Services\Merchant\IMerchantService::class,
            \Modules\Merchant\Services\Merchant\MerchantService::class
        );

        // Cache Key Manager
        $this->app->bind(
            \Modules\Merchant\Cache\KeyManager\IKeyManager::class,
            \Modules\Merchant\Cache\KeyManager\KeyManager::class
        );

        // Document Services
        $this->app->bind(
            \Modules\Merchant\Repositories\Document\IDocumentRepository::class,
            \Modules\Merchant\Repositories\Document\DocumentRepository::class
        );

        $this->app->bind(
            \Modules\Merchant\Services\Document\IDocumentService::class,
            \Modules\Merchant\Services\Document\DocumentService::class
        );

        // File Upload Service
        $this->app->bind(
            \Modules\Merchant\Services\FileUpload\IFileUploadService::class,
            \Modules\Merchant\Services\FileUpload\FileUploadService::class
        );

        // Merchant Setting Services
        $this->app->bind(
            \Modules\Merchant\Repositories\Setting\IMerchantSettingRepository::class,
            \Modules\Merchant\Repositories\Setting\MerchantSettingRepository::class
        );

        $this->app->bind(
            \Modules\Merchant\Services\Setting\IMerchantSettingService::class,
            \Modules\Merchant\Services\Setting\MerchantSettingService::class
        );

        // Profile Ownership Policy
        $this->app->bind(
            \Modules\Merchant\Policies\ProfileOwnership\IProfileOwnershipPolicy::class,
            \Modules\Merchant\Policies\ProfileOwnership\ProfileOwnershipPolicy::class
        );

        // Merchant Ownership Policy
        $this->app->bind(
            \Modules\Merchant\Policies\MerchantOwnership\IMerchantOwnershipPolicy::class,
            \Modules\Merchant\Policies\MerchantOwnership\MerchantOwnershipPolicy::class
        );

        // Shared Merchant Service
        $this->app->bind(
            \App\Shared\Merchant\Services\IMerchantService::class,
            \Modules\Merchant\Services\ForShare\MerchantService::class
        );
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        \Modules\Merchant\Models\Profile::observe(\Modules\Merchant\Observers\ProfileObserver::class);
        \Modules\Merchant\Models\Document::observe(\Modules\Merchant\Observers\DocumentObserver::class);
        \Modules\Merchant\Models\Merchant::observe(\Modules\Merchant\Observers\MerchantObserver::class);
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
