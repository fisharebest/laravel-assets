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

use Fisharebest\LaravelAssets\Filters\RewriteCssUrls;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class RewriteCssUrlsTest extends TestCase {
	/**
	 * Test the filter RewriteCssUrls
	 *
	 * @covers Fisharebest\LaravelAssets\Filters\RewriteCssUrls
	 */
	public function testFilter() {
		$assets = $this->createDefaultAssets();
		$filter = new RewriteCssUrls;

		$this->assertSame('body {background-image: url(../css/foo.png);}', $filter->filter('body {background-image: url(foo.png);}', 'test.css', $assets));
		$this->assertSame('body {background-image: url (../css/foo.png);}', $filter->filter('body {background-image: url (foo.png);}', 'test.css', $assets));
		$this->assertSame('body {background-image: url("../css/foo.png");}', $filter->filter('body {background-image: url("foo.png");}', 'test.css', $assets));

		$this->assertSame('body {background-image: url(data:eek);}', $filter->filter('body {background-image: url(data:eek);}', 'test.css', $assets));
	}

	/**
	 * Test the filter RewriteCssUrls
	 *
	 * @covers Fisharebest\LaravelAssets\Filters\RewriteCssUrls
	 */
	public function testAbsoluteUrl() {
		$assets = $this->createDefaultAssets();
		$filter = new RewriteCssUrls;

		$this->assertSame('body {background-image: url(http://example.com/foo.png);}', $filter->filter('body {background-image: url(foo.png);}', 'http://example.com/test.css', $assets));
	}
}
