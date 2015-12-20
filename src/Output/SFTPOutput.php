<?php

namespace kriskbx\wyn\Output;

use League\Flysystem\Sftp\SftpAdapter;

class SFTPOutput extends FlySystemOutput {
	/**
	 * Constructor.
	 *
	 * @param string $path
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $privateKey
	 * @param int $timeout
	 * @param int $port
	 * @param array $exclude
	 * @param bool $ignore
	 * @param bool $delete
	 * @param bool $versioning
	 * @param bool $encrypt
	 */
	public function __construct( $path, $host, $username, $password = null, $privateKey = null, $timeout = 10, $port = 22, $exclude = [ ], $ignore = true, $delete = true, $versioning = false, $encrypt = false ) {
		parent::__construct( $exclude, $ignore, $delete, $versioning, $encrypt );

		$adapter = new SftpAdapter( [
			'host'          => $host,
			'port'          => $port,
			'username'      => $username,
			'password'      => $password,
			'privateKey'    => $privateKey,
			'root'          => $path,
			'timeout'       => $timeout,
			'directoryPerm' => 0755,
		] );

		$this->setFilesystem( $adapter );
	}
}
