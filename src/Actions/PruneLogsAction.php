<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Actions;

use Skywalker\LogViewer\Contracts\Utilities\Filesystem;

use Skywalker\Support\Foundation\Action;

/**
 * Class     PruneLogsAction
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class PruneLogsAction extends Action
{
    /** @var Filesystem */
    private $filesystem;

    /**
     * PruneLogsAction constructor.
     */
    public function __construct(?Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?? app(Filesystem::class);
    }

    /**
     * Prune log files older than X days.
     *
     * @param  mixed  ...$args
     */
    public function execute(...$args): int
    {
        $arg = $args[0] ?? 0;
        $retention = is_numeric($arg) ? (int) $arg : 0;
        $deleted = 0;
        $dates = $this->filesystem->dates();

        foreach ($dates as $date) {
            if (strtotime($date) < strtotime("-{$retention} days")) {
                if ($this->filesystem->deleteByDate($date)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
