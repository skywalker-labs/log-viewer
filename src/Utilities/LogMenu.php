<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Utilities;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Skywalker\LogViewer\Contracts\Utilities\LogMenu as LogMenuContract;
use Skywalker\LogViewer\Contracts\Utilities\LogStyler as LogStylerContract;
use Skywalker\LogViewer\Entities\Log;

/**
 * Class     LogMenu
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogMenu implements LogMenuContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The config repository instance.
     */
    protected ConfigContract $config;

    /**
     * The log styler instance.
     */
    private LogStylerContract $styler;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * LogMenu constructor.
     */
    public function __construct(ConfigContract $config, LogStylerContract $styler)
    {
        $this->setConfig($config);
        $this->setLogStyler($styler);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the config instance.
     *
     *
     * @return self
     */
    public function setConfig(ConfigContract $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the log styler instance.
     *
     *
     * @return self
     */
    public function setLogStyler(LogStylerContract $styler)
    {
        $this->styler = $styler;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make log menu.
     *
     * @param  bool  $trans
     * @return array<string, mixed>
     */
    public function make(Log $log, $trans = true)
    {
        $items = [];
        $route = $this->config('menu.filter-route');

        foreach ($log->tree($trans) as $level => $item) {
            $items[$level] = array_merge(is_array($item) ? $item : [], [
                'url' => route(is_string($route) ? $route : 'log-viewer::logs.filter', [$log->date, $level]),
                'icon' => $this->isIconsEnabled() ? $this->styler->icon((string) $level)->toHtml() : '',
            ]);
        }

        return $items;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the icons are enabled.
     *
     * @return bool
     */
    private function isIconsEnabled()
    {
        return (bool) $this->config('menu.icons-enabled', false);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get config.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    private function config($key, $default = null)
    {
        return $this->config->get("log-viewer.$key", $default);
    }
}
