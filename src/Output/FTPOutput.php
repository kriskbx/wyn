<?php

namespace kriskbx\wyn\Output;

use League\Flysystem\Adapter\Ftp;

class FTPOutput extends FlySystemOutput {
	/**
	 * Constructor.
	 *
	 * @param array $host
	 * @param bool $username
	 * @param bool $password
	 * @param string $root
	 * @param int $port
	 * @param bool $passive
	 * @param bool $ssl
	 * @param int $timeout
	 * @param array $exclude
	 * @param bool $ignore
	 * @param bool $delete
	 * @param bool|string $versioning
	 * @param bool|string $encrypt
	 */
	public function __construct(
		$host, $username, $password, $root = '/', $port = 21, $passive = true, $ssl = true,
		$timeout = 30, $exclude = [ ], $ignore = true, $delete = true, $versioning = false, $encrypt = false
	) {
		parent::__construct( $exclude, $ignore, $delete, $versioning, $encrypt );

		$this->setFilesystem( new Ftp( [
			'host'     => $host,
			'username' => $username,
			'password' => $password,
			'root'     => $root,
			'port'     => $port,
			'passive'  => $passive,
			'ssl'      => $ssl,
			'timeout'  => $timeout
		] ) );
	}
}
