<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Actions;

use Skywalker\LogViewer\Contracts\LogViewer;
use Skywalker\Support\Foundation\Action;

class SearchLogsAction extends Action
{
    /**
     * The LogViewer instance.
     */
    protected LogViewer $logViewer;

    /**
     * Create a new action instance.
     */
    public function __construct(?LogViewer $logViewer = null)
    {
        /** @var LogViewer $instance */
        $instance = $logViewer ?? app(LogViewer::class);
        $this->logViewer = $instance;
    }

    /**
     * Execute the action.
     *
     * @param  mixed  ...$args
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, \Skywalker\LogViewer\Entities\LogEntry>
     */
    public function execute(...$args)
    {
        /** @var string $date */
        $date = $args[0] ?? '';
        /** @var string $level */
        $level = $args[1] ?? 'all';
        /** @var string|null $query */
        $query = $args[2] ?? null;

        /** @var int $perPage */
        $perPage = config('log-viewer.per-page', 30);

        return $this->logViewer->entries($date, $level)
            ->filter(function ($entry) use ($query) {
                if (empty($query)) {
                    return true;
                }

                /** @var string $query */
                $queryLower = strtolower($query);

                /** @var \Skywalker\LogViewer\Entities\LogEntry $entry */
                return str_contains(strtolower((string) $entry->header), $queryLower) ||
                       str_contains(strtolower((string) $entry->stack), $queryLower);
            })
            ->paginate($perPage);
    }
}
