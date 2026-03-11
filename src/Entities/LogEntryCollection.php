<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Entities;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Skywalker\LogViewer\Helpers\LogParser;

/**
 * Class     LogEntryCollection
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 *
 * @phpstan-ignore-next-line generics.notCompatible
 * @phpstan-extends   LazyCollection<int, LogEntry>
 * @phpstan-implements \IteratorAggregate<int, LogEntry>
 */
class LogEntryCollection extends LazyCollection
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Load raw log entries.
     *
     * @param  string  $raw
     * @return self
     *
     * @phpstan-return self&iterable<int, LogEntry>
     */
    public static function load($raw)
    {
        return new self(function () use ($raw) {
            foreach (LogParser::parse($raw) as $entry) {
                $level = is_string($entry['level'] ?? null) ? $entry['level'] : '';
                $header = is_string($entry['header'] ?? null) ? $entry['header'] : '';
                $stack = is_string($entry['stack'] ?? null) ? $entry['stack'] : null;

                yield new LogEntry(
                    $level,
                    $header,
                    $stack,
                    $entry
                );
            }
        });
    }

    /**
     * Paginate log entries.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, \Skywalker\LogViewer\Entities\LogEntry>
     */
    public function paginate($perPage = 20)
    {
        /** @var \Illuminate\Http\Request $request */
        $request = request();
        $page = $request->get('page', 1);
        $page = is_numeric($page) ? (int) $page : 1;
        $path = $request->url();

        return new LengthAwarePaginator(
            $this->forPage($page, $perPage),
            $this->count(),
            $perPage,
            $page,
            compact('path')
        );
    }

    /**
     * Get filtered log entries by level.
     *
     * @param  string  $level
     * @return self
     *
     * @phpstan-return self&iterable<int, LogEntry>
     */
    public function filterByLevel($level)
    {
        return $this->filter(function (LogEntry $entry) use ($level) {
            return $entry->isSameLevel($level);
        });
    }

    /**
     * Get log entries stats.
     *
     * @return array<string, int>
     */
    public function stats(): array
    {
        /** @var array<string, int> $counters */
        $counters = $this->initStats();

        foreach ($this->groupBy('level') as $level => $entries) {
            /** @var \Illuminate\Support\LazyCollection<int, LogEntry> $entries */
            $levelKey = (string) $level;
            $counters[$levelKey] = $count = $entries->count();
            $counters['all'] += $count;
        }

        return $counters;
    }

    /**
     * Get the log entries navigation tree.
     *
     * @param  bool|false  $trans
     * @return array<string, array{name: string, count: int}>
     */
    public function tree($trans = false)
    {
        $tree = $this->stats();

        array_walk($tree, function (&$count, $level) use ($trans) {
            $count = [
                'name' => $trans ? \log_levels()->get($level) : $level,
                'count' => $count,
            ];
        });

        return $tree;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Init stats counters.
     *
     * @return array<string, int>
     */
    private function initStats()
    {
        $levels = array_merge_recursive(
            ['all'],
            array_keys(\log_viewer()->levels(true))
        );

        return array_map(function () {
            return 0;
        }, array_flip($levels));
    }
}
