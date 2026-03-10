<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Tests\Commands;

use PHPUnit\Framework\Attributes\Test;
use Skywalker\LogViewer\Tests\TestCase;

/**
 * Class     StatsCommandTest
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class StatsCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    #[Test]
    public function it_can_display_stats(): void
    {
        $this->artisan('log-viewer:stats')
            ->assertExitCode(0);
    }
}
