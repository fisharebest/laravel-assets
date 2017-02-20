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
namespace Fisharebest\LaravelAssets\Filters;

use Fisharebest\LaravelAssets\Assets;

class FinalNewline implements FilterInterface {
	/**
	 * Force files to have a trailing end-of-line character.
	 *
	 * "x=3" is valid javascript. "y=4" is valid javascript.  But concatentaing these
	 * gives "x=3y=4" which is invalid.  Therefore we must make sure that all JS files
	 * end with an end-of-line.
	 *
	 * @param string $data      The data to be filtered
	 * @param string $asset_url The original URL for this data
	 * @param Assets $assets    The asset manager object, for access to its config settings and utilities
	 *
	 * @return string
	 */
	public function filter($data, $asset_url, $assets) {
		if (substr($data, -1) === "\n") {
			return $data;
		} else {
			return $data . "\n";
		}
	}
}
