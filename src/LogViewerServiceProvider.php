<?php

declare(strict_types=1);

namespace Skywalker\LogViewer;

use Skywalker\Support\Providers\PackageServiceProvider;

/**
 * Class     LogViewerServiceProvider
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogViewerServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'skywalker-labs';

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'log-viewer';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->registerProvider(Providers\RouteServiceProvider::class);

        $this->registerCommands([
            \Skywalker\LogViewer\Commands\PublishCommand::class,
            \Skywalker\LogViewer\Commands\StatsCommand::class,
            \Skywalker\LogViewer\Commands\CheckCommand::class,
            \Skywalker\LogViewer\Commands\ClearCommand::class,
            \Skywalker\LogViewer\Commands\AlertCommand::class,
            \Skywalker\LogViewer\Commands\PruneCommand::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadTranslations();
        $this->loadViews();

        if ($this->app->runningInConsole()) {
            $this->publishAll();
        }
    }

    /**
     * Get the translations' folder name.
     */
    protected function getTranslationsFolderName(): string
    {
        return 'resources/lang';
    }

    /**
     * Get the translations' path.
     */
    protected function getTranslationsPath(): string
    {
        return realpath(__DIR__.'/../resources/lang') ?: __DIR__.'/../resources/lang';
    }

    /**
     * Get the base views path.
     */
    protected function getViewsPath(): string
    {
        return realpath(__DIR__.'/../resources/views') ?: __DIR__.'/../resources/views';
    }

    /**
     * Load the translations files.
     */
    protected function loadTranslations(): void
    {
        $path = $this->getTranslationsPath();

        $this->loadTranslationsFrom($path, $this->getPackageName());
        $this->loadJsonTranslationsFrom($path);
    }

    /**
     * Load the views files.
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->getPackageName());
    }
}
