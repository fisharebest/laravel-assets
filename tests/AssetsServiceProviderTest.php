<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (c) 2017 Greg Roach
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\LaravelAssets\Tests;

use Fisharebest\LaravelAssets\AssetsServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Mockery;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2017 Greg Roach
 * @license   GPLv3+
 */
class AssetsServiceProviderTest extends TestCase {
	/**
	 * Test booting the service provider.
	 *
	 * @covers Fisharebest\LaravelAssets\AssetsServiceProvider
	 */
	public function testBoot() {
		$app = Mockery::mock(Application::class);
		$service_provider = Mockery::mock(AssetsServiceProvider::class . '[publishes]', [$app])
			->shouldAllowMockingProtectedMethods();

		$service_provider
			->shouldReceive('publishes')
			->with([dirname(__DIR__) . '/src/../config/assets.php' => 'assets.php']);

		$service_provider->boot();
	}

	/**
	 * Test registering the service provider.
	 *
	 * @covers Fisharebest\LaravelAssets\AssetsServiceProvider
	 */
	public function testRegister() {
		$app = Mockery::mock(Application::class);
		$service_provider = Mockery::mock(AssetsServiceProvider::class . '[mergeConfigFrom,commands]', [$app])
			->shouldAllowMockingProtectedMethods();

		$app->shouldReceive('singleton');
		$app->shouldReceive('share');
		$app->shouldReceive('offsetGet');
		$app->shouldReceive('bind');
		$app->shouldReceive('make')->with('assets');

		$service_provider
			->shouldReceive('mergeConfigFrom')
			->with(dirname(__DIR__) . '/src/../config/assets.php', 'assets');
		$service_provider
			->shouldReceive('commands')
			->with(['command.assets.purge']);

		$service_provider->register();
	}
}
