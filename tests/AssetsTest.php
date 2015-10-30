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
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class AssetsTest extends TestCase {
	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testEmpty() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$this->assertSame('d41d8cd98f00b204e9800998ecf8427e', md5(''));

		$this->assertSame('<link rel="stylesheet" href="min/d41d8cd98f00b204e9800998ecf8427e.min.css">', $assets->css());
		$this->assertSame('<script src="min/d41d8cd98f00b204e9800998ecf8427e.min.js"></script>', $assets->js());

		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.css'));
		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.css'));
		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.js'));
		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.js'));

		$this->assertFalse($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.css.gz'));
		$this->assertFalse($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.css.gz'));
		$this->assertFalse($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.js.gz'));
		$this->assertFalse($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.js.gz'));

		$this->assertSame('', $filesystem->read('min/d41d8cd98f00b204e9800998ecf8427e.css'));
		$this->assertSame('', $filesystem->read('min/d41d8cd98f00b204e9800998ecf8427e.min.css'));
		$this->assertSame('', $filesystem->read('min/d41d8cd98f00b204e9800998ecf8427e.js'));
		$this->assertSame('', $filesystem->read('min/d41d8cd98f00b204e9800998ecf8427e.min.js'));
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 *
	 * @expectedException        \InvalidArgumentException
	 * @expectedExceptionMessage Unknown asset type: foo!
	 */
	public function testInvalid() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->add('foo!');
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testAttributes() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$this->assertSame('<link media="print" rel="stylesheet" href="min/d41d8cd98f00b204e9800998ecf8427e.min.css">', $assets->css(null, ['media' => 'print']));
		$this->assertSame('<script async src="min/d41d8cd98f00b204e9800998ecf8427e.min.js"></script>', $assets->js(null, ['async']));
	}


	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testDestinationUrl() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->setDestinationUrl('http://example.com');

		$this->assertSame('<link rel="stylesheet" href="http://example.com/d41d8cd98f00b204e9800998ecf8427e.min.css">', $assets->css());
		$this->assertSame('<script src="http://example.com/d41d8cd98f00b204e9800998ecf8427e.min.js"></script>', $assets->js());
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testGzip() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->setGzipStatic(true);

		$assets->css();
		$assets->js();

		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.css.gz'));
		$this->assertTrue($filesystem->has('min/d41d8cd98f00b204e9800998ecf8427e.min.js.gz'));
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testAdd() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->add(['style1.css', 'style2.css']);
		$assets->add('script.js');

		$filesystem->write('css/style1.css', 'foo');
		$filesystem->write('css/style2.css', 'bar');
		$filesystem->write('js/script.js', 'baz');

		$css = $assets->css();
		$js  = $assets->js();

		$this->assertSame(1, preg_match_all('/href="([^"]+)"/', $css, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));

		$this->assertSame(1, preg_match_all('/src="([^"]+)"/', $js, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testCollections() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->setCollections(['coll' => ['style1.css', 'style2.css', 'script.js']]);
		$assets->add('coll');

		$filesystem->write('css/style1.css', 'foo');
		$filesystem->write('css/style2.css', 'bar');
		$filesystem->write('js/script.js', 'baz');

		$css = $assets->css();
		$js  = $assets->js();

		$this->assertSame(1, preg_match_all('/href="([^"]+)"/', $css, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));

		$this->assertSame(1, preg_match_all('/src="([^"]+)"/', $js, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testAbsoluteUrl() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->add('jquery');

		$js  = $assets->js();

		$this->assertSame(1, preg_match_all('/src="([^"]+)"/', $js, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));
	}

	/**
	 * Test adding and rendering assets.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::add
	 * @covers Fisharebest\LaravelAssets\Assets::css
	 * @covers Fisharebest\LaravelAssets\Assets::js
	 * @covers Fisharebest\LaravelAssets\Assets::processAssets
	 * @covers Fisharebest\LaravelAssets\Assets::checkGroupExists
	 * @covers Fisharebest\LaravelAssets\Assets::concatenateFiles
	 * @covers Fisharebest\LaravelAssets\Assets::hash
	 * @covers Fisharebest\LaravelAssets\Assets::createGzip
	 * @covers Fisharebest\LaravelAssets\Assets::htmlLinks
	 * @covers Fisharebest\LaravelAssets\Assets::convertAttributesToHtml
	 */
	public function testIndividualFiles() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);

		$assets->setEnabled(false);
		$assets->add(['style1.css', 'style2.css']);
		$assets->add('script.js');

		$filesystem->write('css/style1.css', 'foo');
		$filesystem->write('css/style2.css', 'bar');
		$filesystem->write('js/script.js', 'baz');

		$css = $assets->css();
		$js  = $assets->js();

		$this->assertSame(2, preg_match_all('/href="([^"]+)"/', $css, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));
		$this->assertTrue($filesystem->has($matches[1][1]));

		$this->assertSame(1, preg_match_all('/src="([^"]+)"/', $js, $matches));
		$this->assertTrue($filesystem->has($matches[1][0]));
	}
}
