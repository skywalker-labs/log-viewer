<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Utilities;

use Exception;
use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;
use Skywalker\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Skywalker\LogViewer\Exceptions\FilesystemException;
use Skywalker\LogViewer\Helpers\LogParser;
use Skywalker\Support\Filesystem\Filesystem as ToolkitFilesystem;

/**
 * Class     Filesystem
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class Filesystem extends ToolkitFilesystem implements FilesystemContract
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * The base storage path.
     */
    protected string $storagePath;

    /**
     * The log files prefix pattern.
     */
    protected string $prefixPattern;

    /**
     * The log files date pattern.
     */
    protected string $datePattern;

    /**
     * The log files extension.
     */
    protected string $extension;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Filesystem constructor.
     *
     * @param  string  $storagePath
     */
    public function __construct($storagePath)
    {
        $this->setPath($storagePath);
        $this->setPattern();
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the files instance.
     *
     * @return $this
     */
    public function getInstance()
    {
        return $this;
    }

    /**
     * Set the log storage path.
     *
     * @param  string  $storagePath
     * @return $this
     */
    public function setPath($storagePath)
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    /**
     * Get the log pattern.
     */
    public function getPattern(): string
    {
        return $this->prefixPattern.$this->datePattern.$this->extension;
    }

    /**
     * Set the log pattern.
     *
     * @param  string  $date
     * @param  string  $prefix
     * @param  string  $extension
     * @return $this
     */
    public function setPattern(
        $prefix = self::PATTERN_PREFIX,
        $date = self::PATTERN_DATE,
        $extension = self::PATTERN_EXTENSION
    ) {
        $this->setPrefixPattern($prefix);
        $this->setDatePattern($date);
        $this->setExtension($extension);

        return $this;
    }

    /**
     * Set the log date pattern.
     *
     * @param  string  $datePattern
     * @return $this
     */
    public function setDatePattern($datePattern)
    {
        $this->datePattern = $datePattern;

        return $this;
    }

    /**
     * Set the log prefix pattern.
     *
     * @param  string  $prefixPattern
     * @return $this
     */
    public function setPrefixPattern($prefixPattern)
    {
        $this->prefixPattern = $prefixPattern;

        return $this;
    }

    /**
     * Set the log extension.
     *
     * @param  string  $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all log files.
     *
     * @return array<int, string>
     */
    public function all()
    {
        return $this->getFiles('*'.$this->extension);
    }

    public function logs()
    {
        return $this->getFiles($this->getPattern());
    }

    /**
     * List the log files (Only dates).
     *
     * @param  bool  $withPaths
     * @return array<int, string>|array<string, string>
     */
    public function dates($withPaths = false)
    {
        $files = array_reverse($this->logs());
        $dateMap = [];

        foreach ($files as $file) {
            $dateFromFilename = LogParser::extractDate(basename($file));

            // Check if filename matches the date pattern (Standard Log)
            if (preg_match('/'.LogParser::REGEX_DATE_PATTERN.'/', $dateFromFilename)) {
                $dateMap[$dateFromFilename] = $file;

                continue;
            }

            // If filename doesn't have a date (e.g. laravel.log), scan content
            try {
                $content = $this->get($file);

                if ($content) {
                    preg_match_all('/\['.LogParser::REGEX_DATE_PATTERN.'/', $content, $matches);
                    if (! empty($matches[0])) {
                        $dates = array_unique($matches[0]);
                        $baseName = pathinfo($file, PATHINFO_FILENAME);
                        foreach ($dates as $dateStr) {
                            $cleanDate = substr($dateStr, 1);
                            $key = $cleanDate.' ('.$baseName.')';
                            $dateMap[$key] = $file;
                        }
                    }
                }
            } catch (Exception $e) {
                // Squelch errors reading file
            }
        }

        // Sort dates descending
        krsort($dateMap);

        if ($withPaths) {
            return $dateMap;
        }

        return array_keys($dateMap);
    }

    /**
     * Read the log.
     *
     * @param  string  $date
     * @return string
     *
     * @throws \Skywalker\LogViewer\Exceptions\FilesystemException
     */
    public function read($date)
    {
        try {
            $log = $this->get(
                $this->getLogPath($date)
            );
        } catch (Exception $e) {
            throw new FilesystemException($e->getMessage());
        }

        return $log;
    }

    /**
     * Delete the log by date.
     *
     * @return bool
     *
     * @throws \Skywalker\LogViewer\Exceptions\FilesystemException
     */
    public function deleteByDate(string $date)
    {
        $path = $this->getLogPath($date);

        throw_unless(parent::delete($path), FilesystemException::cannotDeleteLog());

        return true;
    }

    /**
     * Clear the log files.
     *
     * @return bool
     */
    public function clear()
    {
        return parent::delete($this->logs());
    }

    /**
     * Get the log file path.
     *
     * @param  string  $date
     * @return string
     */
    public function path($date)
    {
        return $this->getLogPath($date);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get all files.
     *
     * @param  string  $pattern
     * @return array<int, string>
     */
    private function getFiles($pattern)
    {
        $files = $this->glob(
            $this->storagePath.DIRECTORY_SEPARATOR.$pattern,
            defined('GLOB_BRACE') ? GLOB_BRACE : 0
        );

        return array_filter(array_map('realpath', $files));
    }

    /**
     * Get the log file path.
     *
     *
     * @return string
     *
     * @throws \Skywalker\LogViewer\Exceptions\FilesystemException
     */
    private function getLogPath(string $date)
    {
        if (preg_match('/(.+) \((.+)\)$/', $date, $matches)) {
            $date = $matches[1];
            $filename = $matches[2];
            $path = $this->storagePath.DIRECTORY_SEPARATOR.$filename.$this->extension;

            if ($this->exists($path)) {
                $real = realpath($path);
                if (is_string($real)) {
                    return $real;
                }
            }
        }

        $path = $this->storagePath.DIRECTORY_SEPARATOR.$this->prefixPattern.$date.$this->extension;

        if ($this->exists($path)) {
            $real = realpath($path);
            if (is_string($real)) {
                return $real;
            }
        }

        // Try to check if date is the filename
        $path = $this->storagePath.DIRECTORY_SEPARATOR.$date;

        if ($this->exists($path)) {
            $real = realpath($path);
            if (is_string($real)) {
                return $real;
            }
        }

        throw FilesystemException::invalidPath($path);
    }
}
