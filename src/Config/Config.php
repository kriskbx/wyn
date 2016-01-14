<?php


namespace kriskbx\wyn\Config;

use kriskbx\wyn\Contracts\Config\Config as ConfigContract;

abstract class Config implements ConfigContract {

	/**
	 * Default options.
	 *
	 * @var array
	 */
	protected $defaults = [
		'timeout' => 7200
	];

}