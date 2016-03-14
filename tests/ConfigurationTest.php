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
use Fisharebest\LaravelAssets\Filters\MinifyCss;
use Fisharebest\LaravelAssets\Filters\MinifyJs;
use Fisharebest\LaravelAssets\Filters\RewriteCssUrls;
use Fisharebest\LaravelAssets\Loaders\FileGetContents;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2016 Greg Roach
 * @license   GPLv3+
 */
class ConfigurationTest extends TestCase {
    
    	/**
	 * Test getting/setting the "enabled" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getUseResources
	 * @covers Fisharebest\LaravelAssets\Assets::setUseResources
	 */
	public function testUseResourcePath() {
		$assets = $this->createDefaultAssets();
		$this->assertFalse($assets->getUseResources());

		$assets->setUseResources(false);
		$this->assertFalse($assets->getUseResources());

		$assets->setUseResources(true);
		$this->assertTrue($assets->getUseResources());
	}
    
	/**
	 * Test getting/setting the "enabled" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::isEnabled
	 * @covers Fisharebest\LaravelAssets\Assets::setEnabled
	 */
	public function testEnabled() {
		$assets = $this->createDefaultAssets();
		$this->assertTrue($assets->isEnabled());

		$assets->setEnabled(false);
		$this->assertFalse($assets->isEnabled());

		$assets->setEnabled(true);
		$this->assertTrue($assets->isEnabled());
	}

	/**
	 * Test getting/setting the "css_source" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getCssSource
	 * @covers Fisharebest\LaravelAssets\Assets::setCssSource
	 */
	public function testCssSource() {
		$assets = $this->createDefaultAssets();
		$this->assertSame('css', $assets->getCssSource());

		$assets->setCssSource('foo/bar');
		$this->assertSame('foo/bar', $assets->getCssSource());

		$assets->setCssSource('/leading/trailing/slashes/');
		$this->assertSame('leading/trailing/slashes', $assets->getCssSource());
	}

	/**
	 * Test getting/setting the "js_source" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getJsSource
	 * @covers Fisharebest\LaravelAssets\Assets::setJsSource
	 */
	public function testJsSource() {
		$assets = $this->createDefaultAssets();
		$this->assertSame('js', $assets->getJsSource());

		$assets->setJsSource('foo/bar');
		$this->assertSame('foo/bar', $assets->getJsSource());

		$assets->setJsSource('/leading/trailing/slashes/');
		$this->assertSame('leading/trailing/slashes', $assets->getJsSource());
	}

	/**
	 * Test getting/setting the "destination" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getDestination
	 * @covers Fisharebest\LaravelAssets\Assets::setDestination
	 */
	public function testDestination() {
		$assets = $this->createDefaultAssets();
		$this->assertSame('min', $assets->getDestination());

		$assets->setDestination('foo/bar');
		$this->assertSame('foo/bar', $assets->getDestination());

		$assets->setDestination('/leading/trailing/slashes/');
		$this->assertSame('leading/trailing/slashes', $assets->getDestination());
	}

	/**
	 * Test getting/setting the "destination_url" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getDestinationUrl
	 * @covers Fisharebest\LaravelAssets\Assets::setDestinationUrl
	 */
	public function testDestinationUrl() {
		$assets = $this->createDefaultAssets();
		$this->assertSame('', $assets->getDestinationUrl());

		$assets->setDestinationUrl('http://www.example.com');
		$this->assertSame('http://www.example.com', $assets->getDestinationUrl());

		$assets->setDestinationUrl('http://www.example.com/trailing/slashes/');
		$this->assertSame('http://www.example.com/trailing/slashes', $assets->getDestinationUrl());
	}

	/**
	 * Test getting/setting the "css_filters" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getCssFilters
	 * @covers Fisharebest\LaravelAssets\Assets::setCssFilters
	 */
	public function testCssFilters() {
		$assets = $this->createDefaultAssets();

		$this->assertCount(2, $assets->getCssFilters());
		$this->assertInstanceOf(RewriteCssUrls::class, $assets->getCssFilters()[0]);
		$this->assertInstanceOf(MinifyCss::class, $assets->getCssFilters()[1]);

		$assets->setCssFilters([]);
		$this->assertCount(0, $assets->getCssFilters());

		$assets->setCssFilters([new MinifyCss]);
		$this->assertCount(1, $assets->getCssFilters());
		$this->assertInstanceOf(MinifyCss::class, $assets->getCssFilters()[0]);
	}

	/**
	 * Test getting/setting the "js_filters" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getJsFilters
	 * @covers Fisharebest\LaravelAssets\Assets::setJsFilters
	 */
	public function testJsFilters() {
		$assets = $this->createDefaultAssets();

		$this->assertCount(2, $assets->getJsFilters());
		$this->assertInstanceOf(FinalNewline::class, $assets->getJsFilters()[0]);
		$this->assertInstanceOf(MinifyJs::class, $assets->getJsFilters()[1]);

		$assets->setJsFilters([]);
		$this->assertCount(0, $assets->getJsFilters());

		$assets->setJsFilters([new MinifyJs]);
		$this->assertCount(1, $assets->getJsFilters());
		$this->assertInstanceOf(MinifyJs::class, $assets->getJsFilters()[0]);
	}

	/**
	 * Test getting/setting the "loader" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getLoader
	 * @covers Fisharebest\LaravelAssets\Assets::setLoader
	 */
	public function testLoader() {
		$assets = $this->createDefaultAssets();

		$this->assertInstanceOf(FileGetContents::class, $assets->getLoader());

		$assets->setLoader(new FileGetContents);
		$this->assertInstanceOf(FileGetContents::class, $assets->getLoader());
	}

	/**
	 * Test getting/setting the "notifiers" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getNotifiers
	 * @covers Fisharebest\LaravelAssets\Assets::setNotifiers
	 */
	public function testNotifiers() {
		$assets = $this->createDefaultAssets();

		$assets->setNotifiers([]);
		$this->assertCount(0, $assets->getNotifiers());
	}

	/**
	 * Test getting/setting the "inline_threshold" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getInlineThreshold
	 * @covers Fisharebest\LaravelAssets\Assets::setInlineThreshold
	 */
	public function testInlineThreshold() {
		$assets = $this->createDefaultAssets();
		$this->assertSame(0, $assets->getInlineThreshold());

		$assets->setInlineThreshold(2048);
		$this->assertSame(2048, $assets->getInlineThreshold());
	}

	/**
	 * Test getting/setting the "gzip_static" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getGzipStatic
	 * @covers Fisharebest\LaravelAssets\Assets::setGzipStatic
	 */
	public function testGzipStatic() {
		$assets = $this->createDefaultAssets();
		$this->assertSame(0, $assets->getGzipStatic());

		$assets->setGzipStatic(false);
		$this->assertSame(0, $assets->getGzipStatic());

		$assets->setGzipStatic(6);
		$this->assertSame(6, $assets->getGzipStatic());
	}

	/**
	 * Test getting/setting the "collections" option.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::__construct
	 * @covers Fisharebest\LaravelAssets\Assets::getCollections
	 * @covers Fisharebest\LaravelAssets\Assets::setCollections
	 */
	public function testCollections() {
		$assets = $this->createDefaultAssets();
		$this->assertArrayHasKey('jquery', $assets->getCollections());

		$collections = [
			'foo' => ['foo.js', 'foo.css'],
			'bar.css',
		];

		$assets->setCollections($collections);
		$this->assertSame($collections, $assets->getCollections());
	}
}
