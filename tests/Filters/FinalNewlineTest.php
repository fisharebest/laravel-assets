<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (C) 2016 Greg Roach
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

use Fisharebest\LaravelAssets\Filters\FinalNewline;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license   GPLv3+
 */
class FinalNewlineTest extends TestCase {
	/**
	 * Test the final-newline filter
	 *
	 * @covers Fisharebest\LaravelAssets\Filters\FinalNewline
	 */
	public function testFilter() {
		$assets = $this->createDefaultAssets();
		$filter = new FinalNewline;

		$this->assertSame("test\n", $filter->filter('test', 'test.js', $assets));
		$this->assertSame("test\n", $filter->filter("test\n", 'test.js', $assets));
		$this->assertSame("test\r\n", $filter->filter("test\r", 'test.js', $assets));
	}
}
