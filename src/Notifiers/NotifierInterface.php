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
namespace Fisharebest\LaravelAssets\Notifiers;

interface NotifierInterface {
	/**
	 * This function is called whenever an asset file is created.
	 *
	 * If your 'destination_url' does not correspond to the
	 * 'destination' folder, (e.g. Amazon S3, etc), then you
	 * can use this opportunity to copy it.
	 *
	 * @param string $asset The filename of the asset.
	 */
	public function created($asset);
}
