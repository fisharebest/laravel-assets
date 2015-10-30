<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (C) 2015 Greg Roach
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

use Fisharebest\LaravelAssets\Assets;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class TestCase extends PHPUnit_Framework_TestCase {
	/**
	 * Create the default configuration array.
	 *
	 * @return string[]
	 */
	protected function defaultConfiguration() {
		return require __DIR__ . '/../config/assets.php';
	}

	/**
	 * Create an Assets object with default settings
	 */
	protected function createDefaultAssets() {
		$filesystem = new Filesystem(new NullAdapter);

		return new Assets($this->defaultConfiguration(), $filesystem);
	}
}
