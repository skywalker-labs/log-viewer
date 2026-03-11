<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Actions;

use Skywalker\LogViewer\Contracts\Utilities\Filesystem;

use Skywalker\Support\Foundation\Action;

/**
 * Class     ClearLogsAction
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class ClearLogsAction extends Action
{
    /** @var Filesystem */
    private $filesystem;

    /**
     * ClearLogsAction constructor.
     */
    public function __construct(?Filesystem $filesystem = null)
    {
        /** @var Filesystem $fs */
        $fs = $filesystem ?? \app(Filesystem::class);

        $this->filesystem = $fs;
    }

    /**
     * Clear all log files.
     *
     * @param  mixed  ...$args
     */
    public function execute(...$args): bool
    {
        return $this->filesystem->clear();
    }
}
