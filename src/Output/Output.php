<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Contracts\Output\CanWriteStream;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Exceptions\WrongArgumentException;
use kriskbx\wyn\Sync\SyncSettingsConsumer;

abstract class Output implements OutputContract {
	use SyncSettingsConsumer;

	protected $config;

	/**
	 * Does the current implementation supports php streaming?
	 *
	 * @return bool
	 */
	public function supportsStreams() {
		return ( $this instanceof CanWriteStream );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 *
	 * @throws WrongArgumentException
	 */
	public function config( $key ) {
		if ( ! isset( $this->config[ $key ] ) ) {
			throw new WrongArgumentException( $key );
		}

		return $this->config[ $key ];
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setConfig( $key, $value ) {
		$this->config[ $key ] = $value;
	}
}
