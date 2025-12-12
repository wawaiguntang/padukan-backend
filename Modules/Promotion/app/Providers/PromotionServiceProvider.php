<?php

namespace Modules\Promotion\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PromotionServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Promotion';

    protected string $nameLower = 'promotion';

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
        $dbConfig = config('promotion.database');
        $this->app['config']['database.connections.promotion'] = $dbConfig;

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

        // Register repositories
        $this->registerRepositories();

        // Register services
        $this->registerServices();

        // Register shared repositories
        $this->registerSharedRepositories();

        // Register shared services
        $this->registerSharedServices();
    }

    /**
     * Register repository bindings
     */
    protected function registerRepositories(): void
    {
        // Promotion Repository
        $this->app->bind(
            \Modules\Promotion\Repositories\Promotion\IPromotionRepository::class,
            \Modules\Promotion\Repositories\Promotion\PromotionRepository::class
        );

        // Campaign Repository (with caching)
        $this->app->bind(
            \Modules\Promotion\Repositories\Campaign\ICampaignRepository::class,
            \Modules\Promotion\Repositories\Campaign\CachingCampaignRepository::class
        );

        // Promotion Target Repository
        $this->app->bind(
            \Modules\Promotion\Repositories\PromotionTarget\IPromotionTargetRepository::class,
            \Modules\Promotion\Repositories\PromotionTarget\PromotionTargetRepository::class
        );

        // Promotion Usage Repository
        $this->app->bind(
            \Modules\Promotion\Repositories\PromotionUsage\IPromotionUsageRepository::class,
            \Modules\Promotion\Repositories\PromotionUsage\PromotionUsageRepository::class
        );

        // Cache Key Manager
        $this->app->bind(
            \Modules\Promotion\Cache\Promotion\PromotionKeyManager::class,
            \Modules\Promotion\Cache\Promotion\PromotionKeyManager::class
        );

        // Add policy bindings here if needed
        // Example:
        // $this->app->bind(
        //     \Modules\Promotion\Policies\PromotionOwnership\IPromotionOwnershipPolicy::class,
        //     \Modules\Promotion\Policies\PromotionOwnership\PromotionOwnershipPolicy::class
        // );
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // Add service bindings here if needed
        // Example:
        // $this->app->bind(
        //     \Modules\Promotion\Services\Promotion\IPromotionService::class,
        //     \Modules\Promotion\Services\Promotion\PromotionService::class
        // );
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        // Add shared repository bindings here if needed
        // Example:
        // $this->app->bind(
        //     \App\Shared\Repositories\IPromotionRepository::class,
        //     \Modules\Promotion\Repositories\Promotion\PromotionRepository::class
        // );
    }

    /**
     * Register shared service bindings
     */
    protected function registerSharedServices(): void
    {
        // Add shared service bindings here if needed
        // Example:
        // $this->app->bind(
        //     \App\Shared\Promotion\Services\IPromotionService::class,
        //     \Modules\Promotion\Services\Promotion\PromotionService::class
        // );
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([]);
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
     * Register cache key manager binding
     */
    protected function registerCacheKeyManager(): void
    {
        // Cache key manager binding is already handled in registerServices()
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        // Add model observers here if needed
        // Example:
        // \Modules\Promotion\Models\Promotion::observe(\Modules\Promotion\Observers\PromotionObserver::class);
        // \Modules\Promotion\Models\Campaign::observe(\Modules\Promotion\Observers\CampaignObserver::class);
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
     * Register middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Register promotion module middleware
        // $router->aliasMiddleware('auth.jwt', \Modules\Promotion\Http\Middleware\AuthenticationMiddleware::class);
        // $router->aliasMiddleware('auth.authorization', \Modules\Promotion\Http\Middleware\AuthorizationMiddleware::class);
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
