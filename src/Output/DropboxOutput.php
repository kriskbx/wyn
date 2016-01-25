<?php

namespace kriskbx\wyn\Output;

use Dropbox\Client;
use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Output\CanWriteStream;
use League\Flysystem\Dropbox\DropboxAdapter;

class DropboxOutput extends FlySystemOutput implements CanWriteStream, CanReadStream {

	/**
	 * @var
	 */
	protected $accessToken;

	/**
	 * @var
	 */
	protected $appSecret;

	/**
	 * @var null
	 */
	protected $prefix;

	/**
	 * Constructor.
	 *
	 * @param string $accessToken
	 * @param string $appSecret
	 * @param string $prefix
	 */
	public function __construct( $accessToken, $appSecret, $prefix = null ) {
		$this->accessToken = $accessToken;
		$this->appSecret   = $appSecret;
		$this->prefix      = $prefix;

		$client = new Client( $accessToken, $appSecret );

		$this->setFilesystem( new DropboxAdapter( $client, $prefix ) );
	}

	/**
	 * @return null
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @return mixed
	 */
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * @return mixed
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

}
