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

namespace Fisharebest\LaravelAssets\Tests\Commands;

use Fisharebest\LaravelAssets\Assets;
use Fisharebest\LaravelAssets\Commands\Purge;
use Fisharebest\LaravelAssets\Tests\TestCase;

/**
 * @author        Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2021 Greg Roach
 * @license       GPLv3+
 */
class PurgeTest extends TestCase
{
    /**
     * Test the purge command
     *
     * @covers \Fisharebest\LaravelAssets\Commands\Purge
     */
    public function testPurgeCommandCallsPurgeFunction()
    {
        $assets  = $this->createMock(Assets::class);
        $command = new Purge($assets);

        $assets
            ->expects($this->once())
            ->method('purge')
            ->with($command);

        $command->handle();
    }
}
