<?php

namespace kriskbx\wyn\Output;

use Barracuda\Copy\API;
use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Output\CanWriteStream;
use League\Flysystem\Copy\CopyAdapter;

class CopyComOutput extends FlySystemOutput implements CanWriteStream, CanReadStream {
	/**
	 * @var
	 */
	protected $consumerKey;

	/**
	 * @var
	 */
	protected $consumerSecret;

	/**
	 * @var
	 */
	protected $accessToken;

	/**
	 * @var
	 */
	protected $tokenSecret;

	/**
	 * @var null
	 */
	protected $prefix;

	/**
	 * Constructor.
	 *
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $accessToken
	 * @param string $tokenSecret
	 * @param string $prefix
	 */
	public function __construct( $consumerKey, $consumerSecret, $accessToken, $tokenSecret, $prefix = null ) {
		$this->consumerKey    = $consumerKey;
		$this->consumerSecret = $consumerSecret;
		$this->accessToken    = $accessToken;
		$this->tokenSecret    = $tokenSecret;
		$this->prefix         = $prefix;

		$client = new API( $consumerKey, $consumerSecret, $accessToken, $tokenSecret );
		$this->setFilesystem( new CopyAdapter( $client, $prefix ) );
	}

}
