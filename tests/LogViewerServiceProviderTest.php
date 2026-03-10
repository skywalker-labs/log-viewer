<?php

declare(strict_types=1);

namespace Skywalker\LogViewer\Tests;

use PHPUnit\Framework\Attributes\Test;
use Skywalker\LogViewer\LogViewerServiceProvider;

/**
 * Class     LogViewerServiceProviderTest
 *
 * @author   Mradul Sharma <skywalkerlknw@gmail.com>
 */
class LogViewerServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    private LogViewerServiceProvider $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(LogViewerServiceProvider::class);
    }

    protected function tearDown(): void
    {
        unset($this->provider);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Skywalker\Support\Providers\ServiceProvider::class,
            \Skywalker\Support\Providers\PackageServiceProvider::class,
            LogViewerServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    #[Test]
    public function it_can_provides(): void
    {
        $expected = [];

        static::assertSame($expected, $this->provider->provides());
    }
}
