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
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase as TestCase8;

use function class_exists;

if (class_exists(PHPUnit_TestCase::class)) {
    class BaseTestCase extends PHPUnit_TestCase {}
} else {
    class BaseTestCase extends TestCase8 {}
}

/**
 * @author        Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach
 * @license       GPLv3+
 */
class TestCase extends BaseTestCase
{
    /**
     * Create the default configuration array.
     *
     * @return string[]
     */
    protected function defaultConfiguration()
    {
        return require __DIR__ . '/../config/assets.php';
    }

    /**
     * Create an Assets object with default settings
     */
    protected function createDefaultAssets()
    {
        $filesystem = new Filesystem(new NullAdapter);

        return new Assets($this->defaultConfiguration(), $filesystem);
    }
}
