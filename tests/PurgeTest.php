<?php
/**
 * laravel-assets: asset management for Laravel 5
 *
 * Copyright (c) 2021 Greg Roach
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Fisharebest\LaravelAssets\Tests;

use Fisharebest\LaravelAssets\Assets;
use Fisharebest\LaravelAssets\Commands\Purge;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

/**
 * @author        Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach
 * @license       GPLv3+
 */
class PurgeTest extends TestCase
{
    /**
     * Test the purge command.
     *
     * @covers \Fisharebest\LaravelAssets\Assets::purge
     */
    public function testDeleteVerbose()
    {
        $filesystem = new Filesystem(new MemoryAdapter);
        $filesystem->write('min/foo.js', '');

        $assets = new Assets($this->defaultConfiguration(), $filesystem);

        $command = $this->createMock(Purge::class);

        $command
            ->expects($this->exactly(2))
            ->method('option')
            ->withConsecutive(['days'], ['verbose'])
            ->willReturnOnConsecutiveCalls(0, true);

        $command
            ->expects($this->once())
            ->method('info')
            ->with('Deleted: min/foo.js');

        $assets->purge($command);

        $this->assertFalse($filesystem->has('min/foo.js'));
    }

    /**
     * Test the purge command.
     *
     * @covers \Fisharebest\LaravelAssets\Assets::purge
     */
    public function testDeleteSilent()
    {
        $filesystem = new Filesystem(new MemoryAdapter);
        $filesystem->write('min/foo.js', '');

        $assets = new Assets($this->defaultConfiguration(), $filesystem);

        $command = $this->createMock(Purge::class);

        $command
            ->expects($this->exactly(2))
            ->method('option')
            ->withConsecutive(['days'], ['verbose'])
            ->willReturnOnConsecutiveCalls(0, false);

        $assets->purge($command);

        $this->assertFalse($filesystem->has('min/foo.js'));
    }

    /**
     * Test the purge command.
     *
     * @covers \Fisharebest\LaravelAssets\Assets::purge
     */
    public function testRetainVerbose()
    {
        $filesystem = new Filesystem(new MemoryAdapter);
        $filesystem->write('min/foo.js', '');

        $assets = new Assets($this->defaultConfiguration(), $filesystem);

        $command = $this->createMock(Purge::class);
        $command
            ->expects($this->exactly(2))
            ->method('option')
            ->withConsecutive(['days'], ['verbose'])
            ->willReturnOnConsecutiveCalls(1, true);
        $command
            ->expects($this->once())
            ->method('info')
            ->with('Keeping: min/foo.js');

        $assets->purge($command);

        $this->assertTrue($filesystem->has('min/foo.js'));
    }

    /**
     * Test the purge command.
     *
     * @covers \Fisharebest\LaravelAssets\Assets::purge
     */
    public function testRetainSilent()
    {
        $filesystem = new Filesystem(new MemoryAdapter);
        $filesystem->write('min/foo.js', '');

        $assets = new Assets($this->defaultConfiguration(), $filesystem);

        $command = $this->createMock(Purge::class);

        $command
            ->expects($this->exactly(2))
            ->method('option')
            ->withConsecutive(['days'], ['verbose'])
            ->willReturnOnConsecutiveCalls(1, false);

        $assets->purge($command);

        $this->assertTrue($filesystem->has('min/foo.js'));
    }
}
