<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Providers;

use Skywalker\LogViewer\Http\Routes\LogViewerRoute;
use Skywalker\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class     RouteServiceProvider
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class RouteServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Check if routes is enabled
     */
    public function isEnabled(): bool
    {
        return (bool) $this->config('enabled', false);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        if ($this->isEnabled()) {
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get config value by key
     *
     * @param  string  $key
     * @param  mixed|null  $default
     * @return mixed
     */
    private function config($key, $default = null)
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app->make('config');

        return $config->get("log-viewer.route.$key", $default);
    }
}
