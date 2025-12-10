<?php

namespace Modules\Product\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProductServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Product';

    protected string $nameLower = 'product';

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
        $dbConfig = config('product.database');
        $this->app['config']['database.connections.product'] = $dbConfig;

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
        // Category Repository
        $this->app->bind(
            \Modules\Product\Repositories\Category\ICategoryRepository::class,
            \Modules\Product\Repositories\Category\CategoryRepository::class
        );

        // Product Repository
        $this->app->bind(
            \Modules\Product\Repositories\Product\IProductRepository::class,
            \Modules\Product\Repositories\Product\ProductRepository::class
        );

        // Attribute Master Repository
        $this->app->bind(
            \Modules\Product\Repositories\AttributeMaster\IAttributeMasterRepository::class,
            \Modules\Product\Repositories\AttributeMaster\AttributeMasterRepository::class
        );

        // Attribute Custom Repository
        $this->app->bind(
            \Modules\Product\Repositories\AttributeCustom\IAttributeCustomRepository::class,
            \Modules\Product\Repositories\AttributeCustom\AttributeCustomRepository::class
        );

        // Product Variant Repository
        $this->app->bind(
            \Modules\Product\Repositories\ProductVariant\IProductVariantRepository::class,
            \Modules\Product\Repositories\ProductVariant\ProductVariantRepository::class
        );

        // Product Extra Repository
        $this->app->bind(
            \Modules\Product\Repositories\ProductExtra\IProductExtraRepository::class,
            \Modules\Product\Repositories\ProductExtra\ProductExtraRepository::class
        );

        // Product Bundle Repository
        $this->app->bind(
            \Modules\Product\Repositories\ProductBundle\IProductBundleRepository::class,
            \Modules\Product\Repositories\ProductBundle\ProductBundleRepository::class
        );

        // Unit Conversion Repository
        $this->app->bind(
            \Modules\Product\Repositories\UnitConversion\IUnitConversionRepository::class,
            \Modules\Product\Repositories\UnitConversion\UnitConversionRepository::class
        );

        // Product Service Detail Repository
        $this->app->bind(
            \Modules\Product\Repositories\ProductServiceDetail\IProductServiceDetailRepository::class,
            \Modules\Product\Repositories\ProductServiceDetail\ProductServiceDetailRepository::class
        );

        // Product Image Repository
        $this->app->bind(
            \Modules\Product\Repositories\ProductImage\IProductImageRepository::class,
            \Modules\Product\Repositories\ProductImage\ProductImageRepository::class
        );
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // Product Service
        $this->app->bind(
            \Modules\Product\Services\Product\IProductService::class,
            \Modules\Product\Services\Product\ProductService::class
        );

        // Category Service
        $this->app->bind(
            \Modules\Product\Services\Category\ICategoryService::class,
            \Modules\Product\Services\Category\CategoryService::class
        );

        // Attribute Master Service
        $this->app->bind(
            \Modules\Product\Services\AttributeMaster\IAttributeMasterService::class,
            \Modules\Product\Services\AttributeMaster\AttributeMasterService::class
        );

        // Attribute Custom Service
        $this->app->bind(
            \Modules\Product\Services\AttributeCustom\IAttributeCustomService::class,
            \Modules\Product\Services\AttributeCustom\AttributeCustomService::class
        );

        // File Upload Service
        $this->app->bind(
            \Modules\Product\Services\FileUpload\IFileUploadService::class,
            \Modules\Product\Services\FileUpload\FileUploadService::class
        );
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        // Add shared repository bindings here if needed
        // Example:
        // $this->app->bind(
        //     \App\Shared\Repositories\ICategoryRepository::class,
        //     \Modules\Product\Repositories\Category\CategoryRepository::class
        // );
    }

    /**
     * Register service bindings
     */
    protected function registerSharedServices(): void
    {
        // Add shared service bindings here if needed
        // Example:
        // $this->app->bind(
        //     \App\Shared\Product\Services\ICategoryService::class,
        //     \Modules\Product\Services\Category\CategoryService::class
        // );
    }

    /**
     * Register cache key manager binding
     */
    protected function registerCacheKeyManager(): void
    {

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

        // Register product module middleware
        $router->aliasMiddleware('auth.jwt', \Modules\Product\Http\Middleware\AuthenticationMiddleware::class);
        $router->aliasMiddleware('auth.authorization', \Modules\Product\Http\Middleware\AuthorizationMiddleware::class);
        $router->aliasMiddleware('merchant.access', \Modules\Product\Http\Middleware\MerchantAccessMiddleware::class);
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
