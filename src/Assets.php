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
namespace Fisharebest\LaravelAssets;

use Fisharebest\LaravelAssets\Commands\Purge;
use Fisharebest\LaravelAssets\Filters\FilterInterface;
use Fisharebest\LaravelAssets\Loaders\LoaderInterface;
use Fisharebest\LaravelAssets\Notifiers\NotifierInterface;
use InvalidArgumentException;
use League\Flysystem\Filesystem;

class Assets {
	/**
	 * Regular expression to match a CSS url
	 */
	const REGEX_CSS = '/\.css$/i';

	/**
	 * Regular expression to match a JS url
	 */
	const REGEX_JS = '/\.js$/i';

	/**
	 * Regular expression to match a minified CSS url
	 */
	const REGEX_MINIFIED_CSS = '/[.-]min\.css$/i';

	/**
	 * Regular expression to match a minified JS url
	 */
	const REGEX_MINIFIED_JS = '/[.-]min\.js$/i';

	/**
	 * Regular expression to match an external url
	 */
	const REGEX_EXTERNAL_URL = '/^((https?:)?\/\/|data:)/i';

	/**
	 * File type detection options
	 */
	const TYPE_CSS  = 'css';
	const TYPE_JS   = 'js';
	const TYPE_AUTO = 'auto';

	/**
	 * File group options.  Most sites will only use the default group.
	 */
	const GROUP_DEFAULT = '';

	/**
	 * Format HTML links using printf()
	 */
	const FORMAT_CSS_LINK = '<link%s rel="stylesheet" href="%s">';
	const FORMAT_JS_LINK  = '<script%s src="%s"></script>';

	/**
	 * Format inline assets using printf()
	 */
	const FORMAT_CSS_INLINE = '<style>%s</style>';
	const FORMAT_JS_INLINE  = '<script>%s</script>';

	/**
	 * Enable the pipeline and minify functions.
	 *
	 * @var bool
	 */
	private $enabled;

	/**
	 * Where do we read CSS files.
	 *
	 * @var string
	 */
	private $css_source;

	/**
	 * Where do we read JS files.
	 *
	 * @var string
	 */
	private $js_source;

	/**
	 * Where do we write CSS/JS files.
	 *
	 * @var string
	 */
	private $destination;

	/**
	 * Where does the client read CSS/JS files.
	 *
	 * @var string
	 */
	private $destination_url;

	/**
	 * How to process CSS files.
	 *
	 * @var FilterInterface[]
	 */
	private $css_filters;

	/**
	 * How to process JS files.
	 *
	 * @var FilterInterface[]
	 */
	private $js_filters;

	/**
	 * How to load external files.
	 *
	 * @var LoaderInterface
	 */
	private $loader;

	/**
	 * Do something when we create an asset file.
	 *
	 * @var NotifierInterface[]
	 */
	private $notifiers;

	/**
	 * Assets smaller than this will be rendered inline, saving an HTTP request.
	 *
	 * @var int
	 */
	private $inline_threshold;

	/**
	 * Create compressed version of assets, to support the NGINX gzip_static option.
	 *
	 * @var int
	 */
	private $gzip_static;

	/**
	 * Predefined sets of resources.  Can be nested to arbitrary depth.
	 *
	 * @var string[]|array[]
	 */
	private $collections;

	/**
	 * CSS assets to be processed
	 *
	 * @var string[][]
	 */
	private $css_assets = array();

	/**
	 * Javascript assets to be processed
	 *
	 * @var string[][]
	 */
	private $js_assets = array();

	/**
	 * The filesystem corresponding to our public path.
	 *
	 * @var Filesystem
	 */
	private $public;

	/**
	 * Create an asset manager.
	 *
	 * @param array      $config     The local config, merged with the default config
	 * @param Filesystem $filesystem The public filesystem, where we read/write assets
	 */
	public function __construct(array $config, Filesystem $filesystem) {
		$this
			->setEnabled($config['enabled'])
			->setCssSource($config['css_source'])
			->setJsSource($config['js_source'])
			->setDestination($config['destination'])
			->setDestinationUrl($config['destination_url'])
			->setCssFilters($config['css_filters'])
			->setJsFilters($config['js_filters'])
			->setLoader($config['loader'])
			->setNotifiers($config['notifiers'])
			->setInlineThreshold($config['inline_threshold'])
			->setGzipStatic($config['gzip_static'])
			->setCollections($config['collections']);

		$this->public = $filesystem;
	}

	/**
	 * @param string $css_source
	 *
	 * @return Assets
	 */
	public function setCssSource($css_source) {
		$this->css_source = trim($css_source, '/');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCssSource() {
		return $this->css_source;
	}

	/**
	 * @param string $js_source
	 *
	 * @return Assets
	 */
	public function setJsSource($js_source) {
		$this->js_source = trim($js_source, '/');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getJsSource() {
		return $this->js_source;
	}

	/**
	 * @param string $destination
	 *
	 * @return Assets
	 */
	public function setDestination($destination) {
		$this->destination = trim($destination, '/');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDestination() {
		return $this->destination;
	}

	/**
	 * An (optional) absolute URL for fetching generated assets.
	 *
	 * @param string $destination_url
	 *
	 * @return Assets
	 */
	public function setDestinationUrl($destination_url) {
		$this->destination_url = rtrim($destination_url, '/');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDestinationUrl() {
		return $this->destination_url;
	}

	/**
	 * @param FilterInterface[] $css_filters
	 *
	 * @return Assets
	 */
	public function setCssFilters(array $css_filters) {
		$this->css_filters = $css_filters;

		return $this;
	}

	/**
	 * @return FilterInterface[]
	 */
	public function getCssFilters() {
		return $this->css_filters;
	}

	/**
	 * @param FilterInterface[] $js_filters
	 *
	 * @return Assets
	 */
	public function setJsFilters(array $js_filters) {
		$this->js_filters = $js_filters;

		return $this;
	}

	/**
	 * @return FilterInterface[]
	 */
	public function getJsFilters() {
		return $this->js_filters;
	}

	/**
	 * @param LoaderInterface $loader
	 *
	 * @return Assets
	 */
	public function setLoader($loader) {
		$this->loader = $loader;

		return $this;
	}

	/**
	 * @return LoaderInterface
	 */
	public function getLoader() {
		return $this->loader;
	}

	/**
	 * @param NotifierInterface[] $notifiers
	 *
	 * @return Assets
	 */
	public function setNotifiers(array $notifiers) {
		$this->notifiers = $notifiers;

		return $this;
	}

	/**
	 * @return NotifierInterface[]
	 */
	public function getNotifiers() {
		return $this->notifiers;
	}

	/**
	 * @param boolean $enabled
	 *
	 * @return Assets
	 */
	public function setEnabled($enabled) {
		$this->enabled = (bool) $enabled;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}

	/**
	 * @param int $inline_threshold
	 *
	 * @return Assets
	 */
	public function setInlineThreshold($inline_threshold) {
		$this->inline_threshold = (int) $inline_threshold;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getInlineThreshold() {
		return $this->inline_threshold;
	}

	/**
	 * @param int $gzip_static
	 *
	 * @return Assets
	 */
	public function setGzipStatic($gzip_static) {
		$this->gzip_static = (int) $gzip_static;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getGzipStatic() {
		return $this->gzip_static;
	}

	/**
	 * @param array[]|string[] $collections
	 *
	 * @return Assets
	 */
	public function setCollections($collections) {
		$this->collections = $collections;

		return $this;
	}

	/**
	 * @return array[]|string[]
	 */
	public function getCollections() {
		return $this->collections;
	}

	/**
	 * Add one or more assets.
	 *
	 * @param string|string[] $asset A local filename, a remote URL or the name of a collection.
	 * @param string          $type  Force a file type, "css" or "js", instead of using the extension.
	 * @param string          $group Optionally split your assets into multiple groups, such as "head" and "body".
	 *
	 * @return Assets
	 */
	public function add($asset, $type = self::TYPE_AUTO, $group = self::GROUP_DEFAULT) {
		$this->checkGroupExists($group);

		if (is_array($asset)) {
			foreach ($asset as $a) {
				$this->add($a, $type, $group);
			}
		} elseif ($type === self::TYPE_CSS || $type === self::TYPE_AUTO && preg_match(self::REGEX_CSS, $asset)) {
			if (!in_array($asset, $this->css_assets[$group])) {
				$this->css_assets[$group][] = $asset;
			}
		} elseif ($type === self::TYPE_JS || $type === self::TYPE_AUTO && preg_match(self::REGEX_JS, $asset)) {
			if (!in_array($asset, $this->js_assets[$group])) {
				$this->js_assets[$group][] = $asset;
			}
		} elseif (array_key_exists($asset, $this->collections)) {
			$this->add($this->collections[$asset], $type, $group);
		} else {
			throw new InvalidArgumentException('Unknown asset type: ' . $asset);
		}

		return $this;
	}

	/**
	 * Render markup to load the CSS assets.
	 *
	 * @param string   $group      Optionally split your assets into multiple groups, such as "head" and "body".
	 * @param string[] $attributes Optional attributes, such as ['media' => 'print']
	 *
	 * @return string
	 */
	public function css($group = self::GROUP_DEFAULT, array $attributes = []) {
		$this->checkGroupExists($group);

		return $this->processAssets($attributes, $this->css_assets[$group], '.css', $this->getCssSource(), $this->getCssFilters(), self::FORMAT_CSS_LINK, self::FORMAT_CSS_INLINE);
	}

	/**
	 * Render markup to load the JS assets.
	 *
	 * @param string   $group      Optionally split your assets into multiple groups, such as "head" and "body".
	 * @param string[] $attributes Optional attributes, such as ['async']
	 *
	 * @return string
	 */
	public function js($group = self::GROUP_DEFAULT, array $attributes = []) {
		$this->checkGroupExists($group);

		return $this->processAssets($attributes, $this->js_assets[$group], '.js', $this->getJsSource(), $this->getJsFilters(), self::FORMAT_JS_LINK, self::FORMAT_JS_INLINE);
	}

	/**
	 * Render markup to load the CSS or JS assets.
	 *
	 * @param string[]          $attributes    Optional attributes, such as ['async']
	 * @param string[]          $assets        The files to be processed
	 * @param string            $extension     ".css" or ".js"
	 * @param string            $source_dir    The folder containing the source assets
	 * @param FilterInterface[] $filters       How to process these assets
	 * @param string            $format_link   Template for an HTML link to the asset
	 * @param string            $format_inline Template for an inline asset
	 *
	 * @return string
	 */
	private function processAssets(array $attributes, array $assets, $extension, $source_dir, $filters, $format_link, $format_inline) {
		$hashes = [];
		$path   = $this->getDestination();

		foreach ($assets as $asset) {
			if ($this->isAbsoluteUrl($asset)) {
				$hash = $this->hash($asset);
			} else {
				$hash = $this->hash($asset . $this->public->getTimestamp($source_dir . '/' . $asset));
			}
			if (!$this->public->has($path . '/' . $hash . $extension)) {
				if ($this->isAbsoluteUrl($asset)) {
					$data = $this->getLoader()->loadUrl($asset);
				} else {
					$data = $this->public->read($source_dir . '/' . $asset);
				}
				foreach ($filters as $filter) {
					$data = $filter->filter($data, $asset, $this);
				}
				$this->public->write($path . '/' . $hash . $extension, $data);
				$this->public->write($path . '/' . $hash . '.min' . $extension, $data);
			}
			$hashes[] = $hash;
		}

		// The file name of our pipelined asset.
		$hash       = $this->hash(implode('', $hashes));
		$asset_file = $path . '/' . $hash . '.min' . $extension;

		$this->concatenateFiles($path, $hashes, $hash, $extension);
		$this->concatenateFiles($path, $hashes, $hash, '.min' . $extension);

		$this->createGzip($asset_file);

		foreach ($this->notifiers as $notifier) {
			$notifier->created($asset_file);
		}

		if ($this->getDestinationUrl() === '') {
			$url = url($path);
		} else {
			$url = $this->getDestinationUrl();
		}

		if ($this->isEnabled()) {
			$inline_threshold = $this->getInlineThreshold();
			if ($inline_threshold > 0 && $this->public->getSize($asset_file) <= $inline_threshold) {
				return sprintf($format_inline, $this->public->read($asset_file));
			} else {
				return $this->htmlLinks($url, [$hash], '.min' . $extension, $format_link, $attributes);
			}
		} else {
			return $this->htmlLinks($url, $hashes, $extension, $format_link, $attributes);
		}
	}

	/**
	 * Make sure that the specified group (i.e. array key) exists.
	 *
	 * @param string $group
	 */
	private function checkGroupExists($group) {
		if (!array_key_exists($group, $this->css_assets)) {
			$this->css_assets[$group] = [];
		}
		if (!array_key_exists($group, $this->js_assets)) {
			$this->js_assets[$group] = [];
		}
	}

	/**
	 * Concatenate a number of files.
	 *
	 * @param string   $path        subfolder containing assets to be combined
	 * @param string[] $sources     Filenames (without extension) to be combined
	 * @param string   $destination Filename (without extension) to be created
	 * @param string   $extension   ".css", ".min.js", etc.
	 */
	private function concatenateFiles($path, $sources, $destination, $extension) {
		if (!$this->public->has($path . '/' . $destination . $extension)) {
			$data = '';
			foreach ($sources as $source) {
				$data .= $this->public->read($path . '/' . $source . $extension);
			}
			$this->public->write($path . '/' . $destination . $extension, $data);
		}
	}

	/**
	 * Generate a hash, to use as a filename for generated assets.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	private function hash($text) {
		return md5($text);
	}

	/**
	 * Optionally create a .gz version of a file - to support the NGINX gzip_static option.
	 *
	 * @param string $path
	 */
	private function createGzip($path) {
		$gzip = $this->getGzipStatic();

		if ($gzip >= 1 && $gzip <= 9 && function_exists('gzcompress') && !$this->public->has($path . '.gz')) {
			$content    = $this->public->read($path);
			$content_gz = gzcompress($content, $gzip);
			$this->public->write($path . '.gz', $content_gz);
		}
	}

	/**
	 * Generate HTML links to a list of processed asset files.
	 *
	 * @param string   $url       path to the assets
	 * @param string[] $hashes    base filename
	 * @param string   $extension ".css", ".min.js", etc.
	 * @param string   $format
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	private function htmlLinks($url, $hashes, $extension, $format, $attributes) {
		$html_attributes = $this->convertAttributesToHtml($attributes);

		$html_links = '';
		foreach ($hashes as $asset) {
			$html_links .= sprintf($format, $html_attributes, $url . '/' . $asset . $extension);
		}

		return $html_links;
	}

	/**
	 * Convert an array of attributes to HTML.
	 *
	 * @param string[] $attributes
	 *
	 * @return string
	 */
	private function convertAttributesToHtml(array $attributes) {
		$html = '';
		foreach ($attributes as $key => $value) {
			if (is_int($key)) {
				$html .= ' ' . $value;
			} else {
				$html .= ' ' . $key . '="' . $value . '"';
			}
		}

		return $html;
	}

	/**
	 * Is a URL absolute or relative?
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	public function isAbsoluteUrl($url) {
		return preg_match(self::REGEX_EXTERNAL_URL, $url) === 1;
	}

	/**
	 * Normalize a path, removing '.' and '..' folders. e.g.
	 *
	 * "a/b/./c/../../d" becomes "a/d"
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public function normalizePath($url) {
		while (strpos($url, '/./') !== false) {
			$url = str_replace('/./', '/', $url);
		}
		while (strpos($url, '/../') !== false) {
			$url = preg_replace('/[^\/]+\/\.\.\//', '', $url, 1);
		}

		return $url;
	}

	/**
	 * Create a relative path between two URLs.
	 *
	 * e.g. the relative path from "a/b/c" to "a/d" is "../../d"
	 *
	 * @param string $source
	 * @param string $destination
	 *
	 * @return string
	 */
	public function relativePath($source, $destination) {
		if ($source === '') {
			return $destination;
		}

		$parts1 = explode('/', $source);
		$parts2 = explode('/', $destination);

		while (!empty($parts1) && !empty($parts2) && $parts1[0] === $parts2[0]) {
			array_shift($parts1);
			array_shift($parts2);
		}

		return str_repeat('../', count($parts1)) . implode('/', $parts2);
	}

	/**
	 * Purge generated assets older than a given number of days
	 *
	 * @param Purge $command
	 */
	public function purge(Purge $command) {
		$days      = (int) $command->option('days');
		$verbose   = (bool) $command->option('verbose');
		$files     = $this->public->listContents($this->getDestination(), true);
		$timestamp = time() - $days * 86400;

		foreach ($files as $file) {
			if ($this->needsPurge($file, $timestamp)) {
				$this->public->delete($file['path']);
				$command->info('Deleted: ' . $file['path']);
			} elseif ($verbose) {
				$command->info('Keeping: ' . $file['path']);
			}
		}
	}

	/**
	 * @param array $file
	 * @param int   $timestamp
	 *
	 * @return bool
	 */
	private function needsPurge(array $file, $timestamp) {
		$eligible = preg_match(self::REGEX_JS, $file['path']) || preg_match(self::REGEX_CSS, $file['path']);

		return $eligible && $file['timestamp'] <= $timestamp;
	}
}
