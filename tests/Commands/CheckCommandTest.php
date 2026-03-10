<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Tests\Commands;

use PHPUnit\Framework\Attributes\Test;
use Skywalker\LogViewer\Tests\TestCase;

/**
 * Class     CheckCommandTest
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class CheckCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    #[Test]
    public function it_can_check(): void
    {
        $this->artisan('log-viewer:check')
            ->assertExitCode(0);
    }
}
