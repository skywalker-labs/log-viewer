<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Exceptions;

/**
 * Class     LogNotFoundException
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogNotFoundException extends LogViewerException
{
    /**
     * Make the exception.
     */
    public static function make(string $date): self
    {
        return new self("Log not found in this date [{$date}]");
    }
}
