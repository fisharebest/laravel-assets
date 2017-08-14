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

use Fisharebest\LaravelAssets\AssetsFacade;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2017 Greg Roach
 * @license   GPLv3+
 */
class AssetsFacadeTest extends TestCase {
	/**
	 * Test the facade.
	 *
	 * @covers \Fisharebest\LaravelAssets\AssetsFacade
	 */
	public function testFacade() {
		AssetsFacade::setFacadeApplication(['assets' => 'SOMETHING']);

		$this->assertSame('SOMETHING', AssetsFacade::getFacadeRoot());
	}
}
