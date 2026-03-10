<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Contracts\Utilities;

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Skywalker\LogViewer\Entities\Log;

/**
 * Interface  LogMenu
 *
 * @author    Mradul Sharma <skywalkerlknw@gmail.com>
 */
interface LogMenu
{
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
    public function setConfig(ConfigContract $config);

    /**
     * Set the log styler instance.
     *
     *
     * @return self
     */
    public function setLogStyler(LogStyler $styler);

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make log menu.
     *
     * @param  bool  $trans
     * @return array<string, array{name: string, count: int, url: string, icon: string}>
     */
    public function make(Log $log, $trans = true);
}
