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

class RewriteCssUrls implements FilterInterface {
	/**
	 * Rewrite relative URLs in CSS files to take account of their new location.
	 *
	 * CSS files may contain relative URLs, such as "background-image: url(img.jpg)".
	 * If our processed CSS file is in a different location, such as "min" instead of
	 * "css", then we must rewrite this to "background-image: url(../css/img.jpg)".
	 *
	 * @param string $data      The data to be filtered
	 * @param string $asset_url The original URL for this data
	 * @param Assets $assets    The asset manager object, for access to its config settings and utilities
	 *
	 * @return string
	 */
	public function filter($data, $asset_url, $assets) {
		if ($assets->isAbsoluteUrl($asset_url)) {
			$prefix = dirname($asset_url);
		} else {
			$prefix = $assets->relativePath($assets->getDestination(), $assets->getCssSource() . '/' . dirname($asset_url));
		}

		$data = preg_replace_callback([
			'/(\burl\s*\(\s*")([^"]+?)("\s*\))/',
			'/(\burl\s*\(\s*\')([^\']+?)(\'\s*\))/',
			'/(\burl\s*\(\s*)([^\'"]+?)(\s*\))/',
		], function($matches) use ($assets, $prefix) {
			if ($assets->isAbsoluteUrl($matches[2])) {
				return $matches[0];
			} else {
				return $matches[1] . $assets->normalizePath($prefix . '/' . $matches[2]) . $matches[3];
			}
		}, $data);

		return $data;
	}
}
