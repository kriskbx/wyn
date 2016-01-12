<?php

namespace kriskbx\wyn\Contracts\Sync;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;

interface SyncSettings
{

	/**
	 * Get options of this input.
	 *
	 * @return array
	 */
	public static function getOutputBaseOptions();

	/**
	 * Get options of this input.
	 *
	 * @return array
	 */
	public static function getInputBaseOptions();

}
