<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Utilities;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\HtmlString;
use Skywalker\LogViewer\Contracts\Utilities\LogStyler as LogStylerContract;

/**
 * Class     LogStyler
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogStyler implements LogStylerContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The config repository instance.
     */
    protected ConfigContract $config;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new instance.
     */
    public function __construct(ConfigContract $config)
    {
        $this->config = $config;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get config.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    private function get($key, $default = null)
    {
        return $this->config->get("log-viewer.$key", $default);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make level icon.
     */
    public function icon(string $level, ?string $default = null): HtmlString
    {
        $iconClass = $this->get("icons.$level", $default);

        return new HtmlString('<i class="'.(is_string($iconClass) ? $iconClass : '').'"></i>');
    }

    /**
     * Get level color.
     *
     * @param  string  $level
     * @param  string|null  $default
     * @return string
     */
    public function color($level, $default = null)
    {
        $color = $this->get("colors.levels.$level", $default);

        return is_string($color) ? $color : '';
    }

    /**
     * Get strings to highlight.
     *
     * @param  array<int, string>  $default
     * @return array<int, string>
     */
    public function toHighlight(array $default = []): array
    {
        $highlight = $this->get('highlight', $default);
        if (! is_array($highlight)) {
            return [];
        }
        /** @var array<int, string> $strings */
        $strings = array_values(array_filter($highlight, 'is_string'));

        return $strings;
    }
}
