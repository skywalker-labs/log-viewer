<?php

declare(strict_types=1);

namespace Skywalker\LogViewer;

use Illuminate\Pagination\LengthAwarePaginator;
use Skywalker\LogViewer\Contracts\LogViewer as LogViewerContract;
use Skywalker\LogViewer\Contracts\Utilities\Factory as FactoryContract;
use Skywalker\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Skywalker\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Skywalker\LogViewer\Entities\Log;
use Skywalker\LogViewer\Entities\LogCollection;
use Skywalker\LogViewer\Entities\LogEntryCollection;
use Skywalker\LogViewer\Tables\StatsTable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class     LogViewer
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogViewer implements LogViewerContract
{
    /**
     * The callback that should be used to determine the user's role.
     *
     * @var \Closure|null
     */
    public static $authUsing;

    /**
     * Set the callback that should be used to determine the user's role.
     *
     * @return void
     */
    public static function auth(\Closure $callback)
    {
        static::$authUsing = $callback;
    }
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    /**
     * LogViewer Version
     */
    const VERSION = '1.0.0';

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The factory instance.
     */
    protected FactoryContract $factory;

    /**
     * The filesystem instance.
     */
    protected FilesystemContract $filesystem;

    /**
     * The log levels instance.
     */
    protected LogLevelsContract $levels;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new instance.
     */
    public function __construct(
        FactoryContract $factory,
        FilesystemContract $filesystem,
        LogLevelsContract $levels
    ) {
        $this->factory = $factory;
        $this->filesystem = $filesystem;
        $this->levels = $levels;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the log levels.
     *
     * @param  bool  $flip
     */
    public function levels($flip = false): array
    {
        return $this->levels->lists($flip);
    }

    /**
     * Get the translated log levels.
     *
     * @param  string|null  $locale
     */
    public function levelsNames($locale = null): array
    {
        return $this->levels->names($locale);
    }

    /**
     * Set the log storage path.
     *
     * @param  string  $path
     */
    public function setPath($path): self
    {
        $this->factory->setPath($path);

        return $this;
    }

    /**
     * Get the log pattern.
     */
    public function getPattern(): string
    {
        return $this->factory->getPattern();
    }

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     */
    public function setPattern(
        $prefix = FilesystemContract::PATTERN_PREFIX,
        $date = FilesystemContract::PATTERN_DATE,
        $extension = FilesystemContract::PATTERN_EXTENSION
    ): self {
        $this->factory->setPattern($prefix, $date, $extension);

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all logs.
     */
    public function all(): LogCollection
    {
        return $this->factory->all();
    }

    /**
     * Paginate all logs.
     *
     * @param  int  $perPage
     */
    public function paginate($perPage = 30): LengthAwarePaginator
    {
        return $this->factory->paginate($perPage);
    }

    /**
     * Get a log.
     *
     * @param  string  $date
     */
    public function get($date): Log
    {
        return $this->factory->log($date);
    }

    /**
     * Get the log entries.
     *
     * @param  string  $date
     * @param  string  $level
     */
    public function entries($date, $level = 'all'): LogEntryCollection
    {
        return $this->factory->entries($date, $level);
    }

    /**
     * Download a log file.
     *
     * @param  string  $date
     * @param  string|null  $filename
     * @param  array<string, mixed>  $headers
     */
    public function download($date, $filename = null, $headers = []): BinaryFileResponse
    {
        if (is_null($filename)) {
            $prefix = config('log-viewer.download.prefix', 'laravel-');
            $extension = config('log-viewer.download.extension', 'log');

            $filename = sprintf(
                "%s{$date}.%s",
                is_string($prefix) ? $prefix : 'laravel-',
                is_string($extension) ? $extension : 'log'
            );
        }

        $path = $this->filesystem->path($date);

        /** @var \Illuminate\Contracts\Routing\ResponseFactory $factory */
        $factory = response();

        return $factory->download($path, $filename, $headers);
    }

    /**
     * Get logs statistics.
     */
    public function stats(): array
    {
        return $this->factory->stats();
    }

    /**
     * Get logs statistics table.
     *
     * @param  string|null  $locale
     */
    public function statsTable($locale = null): StatsTable
    {
        return $this->factory->statsTable($locale);
    }

    /**
     * Delete the log.
     *
     * @param  string  $date
     */
    public function delete($date): bool
    {
        return $this->filesystem->deleteByDate($date);
    }

    /**
     * Clear the log files.
     */
    public function clear(): bool
    {
        return $this->filesystem->clear();
    }

    /**
     * Get all valid log files.
     */
    public function files(): array
    {
        return $this->filesystem->logs();
    }

    /**
     * List the log files (only dates).
     */
    public function dates(): array
    {
        return $this->factory->dates();
    }

    /**
     * Get logs count.
     */
    public function count(): int
    {
        return $this->factory->count();
    }

    /**
     * Get entries total from all logs.
     *
     * @param  string  $level
     */
    public function total($level = 'all'): int
    {
        return $this->factory->total($level);
    }

    /**
     * Get logs tree.
     *
     * @param  bool  $trans
     */
    public function tree($trans = false): array
    {
        return $this->factory->tree($trans);
    }

    /**
     * Get logs menu.
     *
     * @param  bool  $trans
     */
    public function menu($trans = true): array
    {
        return $this->factory->menu($trans);
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Determine if the log folder is empty or not.
     */
    public function isEmpty(): bool
    {
        return $this->factory->isEmpty();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the LogViewer version.
     */
    public function version(): string
    {
        return self::VERSION;
    }
}
