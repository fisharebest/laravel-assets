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

use Fisharebest\LaravelAssets\Filters\FinalNewline;
use Fisharebest\LaravelAssets\Filters\MinifyCss;
use Fisharebest\LaravelAssets\Filters\MinifyJs;
use Fisharebest\LaravelAssets\Filters\RewriteCssUrls;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2017 Greg Roach
 * @license   GPLv3+
 */
class HelpersTest extends TestCase {

	/**
	 * Test isAbsoluteUrl() helper.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::isAbsoluteUrl
	 */
	public function testIsAbsoluteUrl() {
		$assets = $this->createDefaultAssets();

		$this->assertTrue($assets->isAbsoluteUrl('http://example.com'));
		$this->assertTrue($assets->isAbsoluteUrl('https://example.com'));
		$this->assertTrue($assets->isAbsoluteUrl('//example.com'));
		$this->assertTrue($assets->isAbsoluteUrl('data:some-encoded-data'));
		$this->assertFalse($assets->isAbsoluteUrl('unknown://example.com'));
		$this->assertFalse($assets->isAbsoluteUrl('http:/example.com'));
		$this->assertFalse($assets->isAbsoluteUrl('http/example.com'));
		$this->assertFalse($assets->isAbsoluteUrl('/http/example'));
	}

	/**
	 * Test normalizePath() helper.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::normalizePath
	 */
	public function testNormalizePath() {
		$assets = $this->createDefaultAssets();

		$this->assertSame('', $assets->normalizePath(''));
		$this->assertSame('a', $assets->normalizePath('a'));
		$this->assertSame('a/b/c', $assets->normalizePath('a/b/c'));
		$this->assertSame('/a/b/c/', $assets->normalizePath('/a/b/c/'));
		$this->assertSame('a/b/d', $assets->normalizePath('a/b/c/../d'));
		$this->assertSame('a/d/e', $assets->normalizePath('a/b/c/../../d/e'));
		$this->assertSame('d/e', $assets->normalizePath('a/../b/../c/../d/e'));
		$this->assertSame('a/b', $assets->normalizePath('a/./b'));
		$this->assertSame('a/d', $assets->normalizePath('a/b/./c/../../d'));
		$this->assertSame('a/.../b', $assets->normalizePath('a/.../b'));
	}

	/**
	 * Test relativePath() helper.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::relativePath
	 */
	public function testRelativePath() {
		$assets = $this->createDefaultAssets();

		$this->assertSame('../bar', $assets->relativePath('foo', 'bar'));
		$this->assertSame('bar', $assets->relativePath('', 'bar'));
		$this->assertSame('../', $assets->relativePath('foo', ''));
		$this->assertSame('bar', $assets->relativePath('foo', 'foo/bar'));
	}
}
