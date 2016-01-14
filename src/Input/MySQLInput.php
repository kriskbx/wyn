<?php


namespace kriskbx\wyn\Input;


class MySQLInput extends Input {

	protected $host;

	protected $user;

	protected $password;

	protected $database;

	protected $port;

	protected $socket;

	/**
	 * MySQLInput constructor.
	 *
	 * @param $host
	 * @param $user
	 * @param $password
	 * @param $database
	 * @param $port
	 * @param $socket
	 */
	public function __construct( $host = null, $user, $password, $database = null, $port = 3306, $socket = null ) {
		$this->host     = $host;
		$this->user     = $user;
		$this->password = $password;
		$this->database = $database;
		$this->port     = $port;
		$this->socket   = $socket;
	}

	/**
	 * Check whether a file exists.
	 *
	 * @param string $path
	 *
	 * @return array|bool|null
	 */
	public function has( $path ) {
		// TODO: Implement has() method.
	}

	/**
	 * Read a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function read( $path ) {
		// TODO: Implement read() method.
	}

	/**
	 * List contents of a directory.
	 *
	 * @param string $directory
	 * @param bool $recursive
	 *
	 * @return array
	 */
	public function listContents( $directory = '', $recursive = false ) {
		// TODO: Implement listContents() method.
	}

	/**
	 * Get the timestamp of a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getTimestamp( $path ) {
		// TODO: Implement getTimestamp() method.
	}

	/**
	 * Get filesize.
	 *
	 * @param string $path
	 *
	 * @return int|false
	 */
	public function getSize( $path ) {
		// TODO: Implement getSize() method.
	}
}