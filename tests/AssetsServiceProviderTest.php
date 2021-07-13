<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (c) 2021 Greg Roach
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Fisharebest\LaravelAssets\Tests;

use Fisharebest\LaravelAssets\AssetsServiceProvider;
use Illuminate\Contracts\Foundation\Application;

use stdClass;

use function strtr;

/**
 * @author        Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach
 * @license       GPLv3+
 */
class AssetsServiceProviderTest extends TestCase
{
    /**
     * Test booting the service provider.
     *
     * @covers \Fisharebest\LaravelAssets\AssetsServiceProvider
     */
    public function testBoot()
    {
        $app              = $this->createMock(Application::class);
        $service_provider = new AssetsServiceProvider($app);

        $service_provider->boot();

        $publishes = [
            strtr(__DIR__, ['/tests' => '/src']) . '/../config/assets.php' => 'assets.php',
        ];

        $this->assertSame($publishes, $service_provider::pathsToPublish());
    }

    /**
     * Test registering the service provider.
     *
     * @covers \Fisharebest\LaravelAssets\AssetsServiceProvider
     */
    public function testRegister()
    {
        // Note that the laravel Application object changes over time, so we need
        // to mock calls made for all versions from 5.0 onwards.

        $config = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get', 'set'])
            ->getMock();
        $config
            ->method('get')
            ->with('assets', [])
            ->willReturn($this->defaultConfiguration());
        $config
            ->method('set')
            ->with('assets', $this->defaultConfiguration())
            ->willReturn($this->defaultConfiguration());

        $listener = $this->getMockBuilder(stdClass::class)
            ->setMethods(['listen'])
            ->getMock();
        $listener->method('listen');

        // The Laravel application object implements ArrayAccess, while the interface doesn't.
        eval('interface MyApplication extends \Illuminate\Contracts\Foundation\Application, \ArrayAccess {}');

        $app = $this->createMock('MyApplication');

        $app
            ->method('make')
            ->with('config')
            ->willReturn($config);

        $app
            ->expects($this->once())
            ->method('singleton');

        $map = [
            ['config', $config],
            ['events', $listener],
        ];

        $app
            ->method('offsetGet')
            ->willReturnMap($map);

        $service_provider = new AssetsServiceProvider($app);

        $service_provider->register();
    }
}
