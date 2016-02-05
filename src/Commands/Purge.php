<?php

namespace Fisharebest\LaravelAssets\Commands;

use Fisharebest\LaravelAssets\Assets;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class Purge extends Command {
	/** Required for Laravel 5.0 */
	protected $name = 'assets:purge';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'assets:purge {--days=0 : Only delete files older than this number of days}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete generated CSS and JS files';

	/**
	 * An instance of our assets object.
	 *
	 * @var Assets $assets
	 */
	private $assets;

	/**
	 * Create a command
	 *
	 * @param Assets $assets
	 */
	public function __construct(Assets $assets) {
		parent::__construct();

		$this->assets = $assets;
	}

	/**
	 * Execute the console command.
	 */
	public function handle() {
		$this->assets->purge($this);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions() {
		return [
			['days', 'd', InputArgument::OPTIONAL, 'number of days', 0],
		];
	}
}
