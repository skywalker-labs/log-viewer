<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Exceptions;

/**
 * Class     FilesystemException
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class FilesystemException extends LogViewerException
{
    public static function cannotDeleteLog(): self
    {
        return new self('There was an error deleting the log.');
    }

    public static function invalidPath(string $path): self
    {
        return new self("The log(s) could not be located at : $path");
    }
}
