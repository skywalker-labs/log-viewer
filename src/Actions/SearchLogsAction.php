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
    public function __construct(LogViewer $logViewer)
    {
        $this->logViewer = $logViewer;
    }

    /**
     * Execute the action.
     *
     * @param  string  $date
     * @param  string  $level
     * @param  string|null  $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function execute(...$args)
    {
        [$date, $level, $query] = $args;

        return $this->logViewer->entries($date, $level)
            ->filter(function ($entry) use ($query) {
                if (empty($query)) {
                    return true;
                }

                return str_contains(strtolower($entry->header), strtolower($query)) ||
                       str_contains(strtolower($entry->stack), strtolower($query));
            })
            ->paginate(config('log-viewer.per-page', 30));
    }
}
