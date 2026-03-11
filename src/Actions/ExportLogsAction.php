<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Actions;

use Skywalker\LogViewer\Contracts\Utilities\Filesystem;

use Skywalker\Support\Foundation\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class     ExportLogsAction
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class ExportLogsAction extends Action
{
    /**
     * The filesystem instance.
     */
    protected Filesystem $filesystem;

    /**
     * ExportLogsAction constructor.
     */
    public function __construct(?Filesystem $filesystem = null)
    {
        /** @var Filesystem $fs */
        $fs = $filesystem ?? \app(Filesystem::class);

        $this->filesystem = $fs;
    }

    /**
     * Export log entries for a given date and level.
     *
     * @param  mixed  ...$args
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function execute(...$args): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $arg0 = $args[0] ?? '';
        $arg1 = $args[1] ?? 'all';

        $date = is_string($arg0) ? $arg0 : '';
        $level = is_string($arg1) ? $arg1 : 'all';

        /** @var \Skywalker\LogViewer\Http\Controllers\LogViewerController $controller */
        $controller = \app(\Skywalker\LogViewer\Http\Controllers\LogViewerController::class);

        /** @var \Illuminate\Http\Request $request */
        $request = \request();

        return $controller->export($request, $date, $level);
    }
}
