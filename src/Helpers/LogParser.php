<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Helpers;

/**
 * Class     LogParser
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogParser
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const REGEX_DATE_PATTERN = '\d{4}(-\d{2}){2}';

    const REGEX_TIME_PATTERN = '\d{2}(:\d{2}){2}';

    const REGEX_DATETIME_PATTERN = self::REGEX_DATE_PATTERN.' '.self::REGEX_TIME_PATTERN;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Parsed data.
     *
     * @var array<int, array<string, mixed>>
     */
    protected static $parsed = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param  string  $raw
     * @param  string  $channel
     * @return array<int, array<string, mixed>>
     */
    public static function parse($raw, $channel = 'laravel')
    {
        static::$parsed = [];
        $pattern = config("log-viewer.channels.{$channel}.pattern");

        if (! is_string($pattern) || ! $pattern) {
            // Fallback to basic Laravel pattern if channel not found
            $pattern = '/^\[(?P<datetime>.*?)\] (?P<env>\w+)\.(?P<level>\w+): (?P<header>.*)/m';
        }

        // Split by lines and try to match
        $lines = explode("\n", $raw);
        $currentEntry = null;

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            if (preg_match($pattern, $line, $matches)) {
                if ($currentEntry) {
                    static::$parsed[] = $currentEntry;
                }

                $currentEntry = [
                    'datetime' => $matches['datetime'] ?? '',
                    'level' => strtolower($matches['level'] ?? 'info'),
                    'env' => $matches['env'] ?? 'local',
                    'header' => $matches['header'] ?? '',
                    'ip' => $matches['ip'] ?? null,
                    'cid' => $matches['cid'] ?? null,
                    'stack' => '',
                ];
            } elseif ($currentEntry) {
                // It's a stack trace or continuation
                $currentEntry['stack'] .= $line."\n";
            }
        }

        if ($currentEntry) {
            static::$parsed[] = $currentEntry;
        }

        return array_reverse(static::$parsed);
    }

    /**
     * Extract the date from a string.
     */
    public static function extractDate(string $string): string
    {
        $extracted = preg_replace('/.*('.self::REGEX_DATE_PATTERN.').*/', '$1', $string);

        return is_string($extracted) ? $extracted : $string;
    }

    /* Removed unused hasLogLevel */
}
