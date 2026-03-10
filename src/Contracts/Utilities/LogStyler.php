<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Contracts\Utilities;

/**
 * Interface  LogStyler
 *
 * @author    Mradul Sharma <skywalkerlknw@gmail.com>
 */
interface LogStyler
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make level icon.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function icon(string $level, ?string $default = null);

    /**
     * Get level color.
     *
     * @param  string  $level
     * @param  string|null  $default
     * @return string
     */
    public function color($level, $default = null);

    /**
     * Get strings to highlight.
     *
     * @param  array<int, string>  $default
     * @return array<int, string>
     */
    public function toHighlight(array $default = []);
}
