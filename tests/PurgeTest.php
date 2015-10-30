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
use Fisharebest\LaravelAssets\Commands\Purge;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Mockery;

/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class PurgeTest extends TestCase {
	/**
	 * Test the purge command.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::purge
	 */
	public function testDeleteVerbose() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);
		$command    = Mockery::mock(Purge::class);

		$filesystem->write('min/foo.js', '');

		$command->shouldReceive('option')->with('days')->andReturn(0);
		$command->shouldReceive('option')->with('verbose')->andReturn(true);
		$command->shouldReceive('info')->with('Deleted: min/foo.js');

		$assets->purge($command);

		$this->assertFalse($filesystem->has('min/foo.js'));
	}

	/**
	 * Test the purge command.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::purge
	 */
	public function testDeleteSilent() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);
		$command    = Mockery::mock(Purge::class);

		$filesystem->write('min/foo.js', '');

		$command->shouldReceive('option')->with('days')->andReturn(0);
		$command->shouldReceive('option')->with('verbose')->andReturn(false);
		$command->shouldReceive('info')->with('Deleted: min/foo.js');

		$assets->purge($command);

		$this->assertFalse($filesystem->has('min/foo.js'));
	}

	/**
	 * Test the purge command.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::purge
	 */
	public function testRetainVerbose() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);
		$command    = Mockery::mock(Purge::class);

		$filesystem->write('min/foo.js', '');

		$command->shouldReceive('option')->with('days')->andReturn(1);
		$command->shouldReceive('option')->with('verbose')->andReturn(true);
		$command->shouldReceive('info')->with('Keeping: min/foo.js');

		$assets->purge($command);

		$this->assertTrue($filesystem->has('min/foo.js'));
	}

	/**
	 * Test the purge command.
	 *
	 * @covers Fisharebest\LaravelAssets\Assets::purge
	 */
	public function testRetainSilent() {
		$filesystem = new Filesystem(new MemoryAdapter);
		$assets     = new Assets($this->defaultConfiguration(), $filesystem);
		$command    = Mockery::mock(Purge::class);

		$filesystem->write('min/foo.js', '');

		$command->shouldReceive('option')->with('days')->andReturn(1);
		$command->shouldReceive('option')->with('verbose')->andReturn(false);

		$assets->purge($command);

		$this->assertTrue($filesystem->has('min/foo.js'));
	}
}
