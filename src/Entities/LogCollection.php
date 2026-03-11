<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Entities;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Skywalker\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Skywalker\LogViewer\Exceptions\LogNotFoundException;

/**
 * Class     LogCollection
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 *
 * @phpstan-extends   LazyCollection
 * @phpstan-implements \IteratorAggregate<string, Log>
 */
class LogCollection extends LazyCollection
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    private FilesystemContract $filesystem;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * LogCollection constructor.
     *
     * @param  \Closure|array<string, Log>|iterable<string, Log>|null  $source
     */
    public function __construct($source = null)
    {
        /** @var FilesystemContract $filesystem */
        $filesystem = \app(FilesystemContract::class);

        $this->setFilesystem($filesystem);

        if (is_null($source)) {
            $source = function () {
                foreach ($this->filesystem->dates(true) as $date => $path) {
                    // Use path to read content directly, avoiding getLogPath($date) failure for virtual dates
                    $content = file_get_contents((string) $path);
                    yield (string) $date => Log::make((string) $date, (string) $path, is_string($content) ? $content : '');
                }
            };
        }

        parent::__construct($source);
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Set the filesystem instance.
     *
     * @param  FilesystemContract  $filesystem
     * @return $this
     */
    public function setFilesystem(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get a log.
     *
     * @param  string  $date
     * @param  mixed|null  $default
     * @return \Skywalker\LogViewer\Entities\Log|null
     *
     * @throws \Skywalker\LogViewer\Exceptions\LogNotFoundException
     */
    public function get($date, $default = null)
    {
        if (! $this->has($date)) {
            throw LogNotFoundException::make($date);
        }

        /** @var Log|null $log */
        $log = parent::get($date, $default);

        return $log;
    }

    /**
     * Paginate logs.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, \Skywalker\LogViewer\Entities\Log>
     */
    public function paginate($perPage = 30)
    {
        /** @var \Illuminate\Http\Request $request */
        $request = \request();

        $page = $request->get('page', 1);
        $page = is_numeric($page) ? (int) $page : 1;
        $path = $request->url();

        /** @var array<int, Log> $items */
        $items = array_values($this->forPage($page, $perPage)->all());

        return new LengthAwarePaginator(
            $items,
            $this->count(),
            $perPage,
            $page,
            compact('path')
        );
    }

    /**
     * Get a log (alias).
     *
     * @see get()
     *
     * @param  string  $date
     * @return \Skywalker\LogViewer\Entities\Log
     */
    public function log($date)
    {
        /** @var Log $log */
        $log = $this->get($date);

        return $log;
    }

    /**
     * Get log entries.
     *
     * @param  string  $date
     * @param  string  $level
     * @return \Skywalker\LogViewer\Entities\LogEntryCollection<int, \Skywalker\LogViewer\Entities\LogEntry>
     */
    public function entries($date, $level = 'all')
    {
        /** @var Log $log */
        $log = $this->get($date);

        return $log->entries($level);
    }

    /**
     * Get logs statistics.
     *
     * @return array<string, array<string, int>>
     */
    public function stats()
    {
        $stats = [];

        foreach ($this->all() as $date => $log) {
            /** @var \Skywalker\LogViewer\Entities\Log $log */
            $stats[$date] = $log->stats();
        }

        return $stats;
    }

    /**
     * List the log files (dates).
     *
     * @return array<int, string>
     */
    public function dates()
    {
        /** @var array<int, string> $keys */
        $keys = $this->keys()->toArray();

        return $keys;
    }

    /**
     * Get entries total.
     *
     * @param  string  $level
     * @return int
     */
    public function total($level = 'all')
    {
        /** @var int|float $total */
        $total = $this->sum(function (Log $log) use ($level) {
            return $log->entries($level)->count();
        });

        return (int) $total;
    }

    /**
     * Get logs tree.
     *
     * @param  bool  $trans
     * @return array<string, array<string, array{name: string, count: int}>>
     */
    public function tree($trans = false)
    {
        $tree = [];

        foreach ($this->all() as $date => $log) {
            /** @var \Skywalker\LogViewer\Entities\Log $log */
            $tree[$date] = $log->tree($trans);
        }

        return $tree;
    }

    /**
     * Get logs menu.
     *
     * @param  bool  $trans
     * @return array<string, array<string, array{name: string, count: int, url: string, icon: string}>>
     */
    public function menu($trans = true)
    {
        $menu = [];

        foreach ($this->all() as $date => $log) {
            /** @var \Skywalker\LogViewer\Entities\Log $log */
            $menu[$date] = $log->menu($trans);
        }

        return $menu;
    }
}
