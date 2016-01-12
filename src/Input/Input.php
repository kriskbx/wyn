<?php

namespace kriskbx\wyn\Input;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Exceptions\WrongArgumentException;
use kriskbx\wyn\Sync\SyncSettingsConsumer;
use PhpSpec\Exception\Exception;

abstract class Input implements InputContract {
	use SyncSettingsConsumer;

	protected $config;

	/**
	 * Does the current implementation supports php streaming?
	 *
	 * @return bool
	 */
	public function supportsStreams() {
		return ( $this instanceof CanReadStream );
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
	 * Set something in the config.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setConfig( $key, $value ) {
		$this->config[ $key ] = $value;
	}
}
