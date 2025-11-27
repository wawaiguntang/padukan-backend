<?php

namespace Modules\Authentication\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AuthenticationServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Authentication';

    protected string $nameLower = 'authentication';

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
        $dbConfig = config('authentication.database');
        $this->app['config']['database.connections.authentication'] = $dbConfig;

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

        // Register cache key manager
        $this->registerCacheKeyManager();

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
        $this->app->bind(
            \Modules\Authentication\Repositories\User\IUserRepository::class,
            \Modules\Authentication\Repositories\User\UserRepository::class
        );

        $this->app->bind(
            \Modules\Authentication\Repositories\Verification\IVerificationRepository::class,
            \Modules\Authentication\Repositories\Verification\VerificationRepository::class
        );

        $this->app->bind(
            \Modules\Authentication\Repositories\PasswordReset\IPasswordResetRepository::class,
            \Modules\Authentication\Repositories\PasswordReset\PasswordResetRepository::class
        );
    }

    /**
     * Register service bindings
     */
    protected function registerServices(): void
    {
        // JWT Service
        $this->app->bind(
            \Modules\Authentication\Services\JWT\IJWTService::class,
            \Modules\Authentication\Services\JWT\JWTService::class
        );

        // User Service
        $this->app->bind(
            \Modules\Authentication\Services\User\IUserService::class,
            \Modules\Authentication\Services\User\UserService::class
        );

        // Verification Service
        $this->app->bind(
            \Modules\Authentication\Services\Verification\IVerificationService::class,
            \Modules\Authentication\Services\Verification\VerificationService::class
        );

        // Password Reset Service
        $this->app->bind(
            \Modules\Authentication\Services\PasswordReset\IPasswordResetService::class,
            \Modules\Authentication\Services\PasswordReset\PasswordResetService::class
        );
    }

    /**
     * Register shared repository bindings
     */
    protected function registerSharedRepositories(): void
    {
        $this->app->bind(
            \App\Shared\Repositories\IUserRepository::class,
            \Modules\Authentication\Repositories\User\UserRepository::class
        );
    }

    /**
     * Register service bindings
     */
    protected function registerSharedServices(): void
    {
        // JWT Service
        $this->app->bind(
            \App\Shared\Authentication\Services\IJWTService::class,
            \Modules\Authentication\Services\JWT\JWTService::class
        );
    }

    /**
     * Register cache key manager binding
     */
    protected function registerCacheKeyManager(): void
    {
        $this->app->bind(
            \Modules\Authentication\Cache\KeyManager\IKeyManager::class,
            \Modules\Authentication\Cache\KeyManager\KeyManager::class
        );
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
        $langPath = resource_path('lang/modules/'.$this->nameLower);

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
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

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

        $router->aliasMiddleware('throttle.otp', \Modules\Authentication\Http\Middleware\ThrottleOtp::class);
        $router->aliasMiddleware('jwt.auth', \Modules\Authentication\Http\Middleware\JWTMiddleware::class);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
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
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}

