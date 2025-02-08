<?php

namespace CSlant\GithubProject\Providers;

use Illuminate\Support\ServiceProvider;

class GithubProjectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerAssetLoading();

        $this->registerAssetPublishing();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerConfigs();

        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return null|array<string>
     */
    public function provides(): ?array
    {
        return ['github-project'];
    }

    /**
     * @return void
     */
    protected function registerCommands(): void
    {
        $this->commands([
            //
        ]);
    }

    /**
     * @return void
     */
    protected function registerAssetPublishing(): void
    {
        $configPath = __DIR__.'/../../config/github-project.php';
        $this->publishes([
            $configPath => config_path('github-project.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../lang' => resource_path('lang/packages/github-project'),
        ], 'lang');
    }

    /**
     * @return void
     */
    protected function registerAssetLoading(): void
    {
        $routePath = __DIR__.'/../../routes/github-project.php';
        if (file_exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'github-project');
    }

    /**
     * Register configs.
     *
     * @return void
     */
    protected function registerConfigs(): void
    {
        $configDir = __DIR__.'/../../config';
        $files = scandir($configDir);

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $configName = pathinfo($file, PATHINFO_FILENAME);
                $configPath = $configDir.'/'.$file;

                if (file_exists(config_path($configName.'.php'))) {
                    config()->set($configName, array_merge(
                        is_array(config($configName)) ? config($configName) : [],
                        require $configPath
                    ));
                } else {
                    $this->mergeConfigFrom($configPath, $configName);
                }
            }
        }
    }
}
