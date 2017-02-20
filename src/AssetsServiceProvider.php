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
namespace Fisharebest\LaravelAssets;

use Fisharebest\LaravelAssets\Commands\Purge;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

class AssetsServiceProvider extends ServiceProvider {
	/**
	 * Perform post-registration booting of services.
	 */
	public function boot() {
		// Allow artisan to publish our config file using the "vendor:publish" command.
		$this->publishes([
			__DIR__ . '/../config/assets.php' => config_path('assets.php'),
		]);
	}

	/**
	 * Register bindings in the container.
	 */
	public function register() {
		// Merge our default configuration.
		$this->mergeConfigFrom(__DIR__ . '/../config/assets.php', 'assets');

		// Bind our component into the IoC container.
		$this->app->singleton('assets', function($app) {
			$filesystem = new Filesystem(new Local(public_path()), ['visibility' => AdapterInterface::VISIBILITY_PUBLIC]);

			return new Assets($app['config']['assets'], $filesystem);
		});

		// Command-line functions
		// Don't use array access here - it is hard to mock / unit-test.  Use bind() and make() instead.
		$this->app->bind('command.assets.purge', function(Application $app) {
			return new Purge($app->make('assets'));
		});

		$this->commands(['command.assets.purge']);
	}
}
