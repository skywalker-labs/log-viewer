<?php

declare(strict_types=1);

use Skywalker\LogViewer\Contracts;

if (! function_exists('log_viewer')) {
    /**
     * Get the LogViewer instance.
     *
     * @return \Skywalker\LogViewer\Contracts\LogViewer
     */
    function log_viewer()
    {
        /** @var \Skywalker\LogViewer\Contracts\LogViewer $instance */
        $instance = app(Contracts\LogViewer::class);

        return $instance;
    }
}

if (! function_exists('log_levels')) {
    /**
     * Get the LogLevels instance.
     *
     * @return \Skywalker\LogViewer\Contracts\Utilities\LogLevels
     */
    function log_levels()
    {
        /** @var \Skywalker\LogViewer\Contracts\Utilities\LogLevels $instance */
        $instance = app(Contracts\Utilities\LogLevels::class);

        return $instance;
    }
}

if (! function_exists('log_menu')) {
    /**
     * Get the LogMenu instance.
     *
     * @return \Skywalker\LogViewer\Contracts\Utilities\LogMenu
     */
    function log_menu()
    {
        /** @var \Skywalker\LogViewer\Contracts\Utilities\LogMenu $instance */
        $instance = app(Contracts\Utilities\LogMenu::class);

        return $instance;
    }
}

if (! function_exists('log_styler')) {
    /**
     * Get the LogStyler instance.
     *
     * @return \Skywalker\LogViewer\Contracts\Utilities\LogStyler
     */
    function log_styler()
    {
        /** @var \Skywalker\LogViewer\Contracts\Utilities\LogStyler $instance */
        $instance = app(Contracts\Utilities\LogStyler::class);

        return $instance;
    }
}
