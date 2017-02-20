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

class NoAction implements NotifierInterface {
	/**
	 * This notifier doesn't actually do anything.
	 * It's used by the unit tests.
	 *
	 * You can use it as a template to create your own notifier.
	 *
	 * @param string $asset The filename of the asset.
	 */
	public function created($asset) {
		// $asset was just created.  Copy it somewhere.
	}
}
