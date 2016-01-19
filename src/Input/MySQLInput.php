<?php


namespace kriskbx\wyn\Input;


use Ifsnop\Mysqldump\Mysqldump;
use PDO;

class MySQLInput extends Input {

	protected $host;

	protected $user;

	protected $password;

	protected $database;

	protected $port;

	protected $socket;

	protected $pdo;

	protected $fileTable;

	protected $dumper;

	protected $dumperData = [ ];

	/**
	 * MySQLInput constructor.
	 *
	 * @param $host
	 * @param $user
	 * @param $password
	 * @param $database
	 * @param int $port
	 * @param $socket
	 * @param string $compress
	 * @param bool $no_data
	 * @param bool $add_drop_table
	 * @param bool $single_transaction
	 * @param bool $lock_tables
	 * @param bool $add_locks
	 * @param bool $extended_insert
	 * @param bool $complete_insert
	 * @param bool $disable_keys
	 * @param bool $no_create_info
	 * @param bool $skip_triggers
	 * @param bool $add_drop_trigger
	 * @param bool $routines
	 * @param bool $hex_blob
	 * @param bool $skip_tz_utc
	 * @param bool $no_autocommit
	 * @param string $default_character_set
	 * @param bool $skip_comments
	 * @param bool $skip_dump_date
	 */
	public function __construct(
		$host = null, $user, $password, $database = null, $port = 3306, $socket = null,
		$compress = 'None', $no_data = false, $add_drop_table = false, $single_transaction = true,
		$lock_tables = false, $add_locks = true, $extended_insert = true,
		$disable_keys = true, $no_create_info = false, $skip_triggers = false, $add_drop_trigger = true,
		$routines = false, $hex_blob = true, $skip_tz_utc = false, $no_autocommit = true, $default_character_set = 'utf8',
		$skip_comments = false, $skip_dump_date = false
	) {
		$this->host     = $host;
		$this->user     = $user;
		$this->password = $password;
		$this->port     = $port;
		$this->socket   = $socket;

		$this->dumperData = [
			'compress'              => $compress,
			'no-data'               => $no_data,
			'add-drop-table'        => $add_drop_table,
			'single-transaction'    => $single_transaction,
			'lock-tables'           => $lock_tables,
			'add-locks'             => $add_locks,
			'extended-insert'       => $extended_insert,
			'disable-keys'          => $disable_keys,
			'no-create-info'        => $no_create_info,
			'skip-triggers'         => $skip_triggers,
			'add-drop-trigger'      => $add_drop_trigger,
			'routines'              => $routines,
			'hex-blob'              => $hex_blob,
			'skip-tz-utc'           => $skip_tz_utc,
			'no-autocommit'         => $no_autocommit,
			'default-character-set' => $default_character_set,
			'skip-comments'         => $skip_comments,
			'skip-dump-date'        => $skip_dump_date
		];

		$this->database = $this->getDatabase( $database );
		$this->setFileTable();
	}

	/**
	 * Get DSN.
	 *
	 * @param string $database
	 *
	 * @return string
	 */
	protected function getDsn( $database = '' ) {
		if ( $database ) {
			$database = ';dbname=' . $database;
		}

		if ( $this->socket ) {
			return 'mysql:unix_socket=' . $this->socket . $database;
		}

		return 'mysql:host=' . $this->host . ';port=' . $this->port . $database;
	}

	/**
	 * Connect.
	 */
	protected function connect() {
		if ( ! $this->pdo ) {
			$this->pdo = new PDO( $this->getDsn(), $this->user, $this->password );
		}
	}

	/**
	 * Get an array of available databases.
	 *
	 * @return array
	 */
	protected function databases() {
		$databases = [ ];

		$this->connect();
		$query = $this->pdo->query( 'SHOW DATABASES' );

		while ( ( $database = $query->fetchColumn( 0 ) ) !== false ) {
			$databases[] = $database;
		}

		return $databases;
	}

	/**
	 * Get the databases in the right format.
	 *
	 * @param string|array $database
	 *
	 * @return array
	 */
	protected function getDatabase( $database = null ) {
		if ( ! $database ) {
			return $this->databases();
		}

		if ( is_array( $database ) ) {
			return $database;
		}

		return [ $database ];
	}

	/**
	 * @param $database
	 *
	 * @return int
	 */
	protected function getDatabaseSize( $database ) {
		if ( in_array( 'information_schema', $this->database ) ) {
			$this->pdo->query( 'USE information_schema' );
			$query = $this->pdo->query(
				"SELECT Sum(data_length + index_length) \"size\"
				FROM information_schema.tables
				WHERE table_schema = \"$database\""
			);

			return $query->fetchColumn( 0 );
		}

		return 0;
	}

	/**
	 * @param $database
	 *
	 * @return int
	 */
	protected function getDatabaseLastModified( $database ) {
		if ( in_array( 'information_schema', $this->database ) ) {
			$this->pdo->query( 'USE information_schema' );
			$query = $this->pdo->query(
				"SELECT MAX(update_time) \"update\", MAX(create_time) \"create\"
				FROM information_schema.tables
				WHERE table_schema = \"$database\""
			);

			$result    = $query->fetch();
			$timestamp = strtotime( max( $result['create'], $result['update'] ) );

			return ( $timestamp ? $timestamp : time() );
		}

		return time();
	}

	/**
	 * Set the "table" of "files".
	 */
	protected function setFileTable() {
		foreach ( $this->database as $database ) {
			$this->fileTable[ $database . '.sql' ] = [
				'database'  => $database,
				'size'      => $this->getDatabaseSize( $database ),
				'timestamp' => $this->getDatabaseLastModified( $database )
			];
		}
	}

	/**
	 * Check whether a file exists.
	 *
	 * @param string $path
	 *
	 * @return array|bool|null
	 */
	public function has( $path ) {
		return array_key_exists( $path, $this->fileTable );
	}

	/**
	 * Read a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function read( $path ) {
		$database = str_ireplace( '.sql', '', $path );

		$this->dumper = new Mysqldump(
			$this->getDsn( $database ),
			$this->user,
			$this->password,
			$this->dumperData
		);

		$file = sys_get_temp_dir() . '/wyn_mysql_dump_' . $database . '_' . uniqid() . '.sql';
		$this->dumper->start( $file );

		$data = file_get_contents( $file );
		unlink( $file );

		return $data;
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
		$return = [ ];

		foreach ( $this->fileTable as $file => $fileData ) {
			$return[] = [
				'type'      => 'file',
				'path'      => $file,
				'timestamp' => $fileData['timestamp'],
				'size'      => $fileData['size'],
				'dirname'   => '',
				'basename'  => $file,
				'extension' => '.sql',
				'filename'  => str_ireplace( '.sql', '', $file )
			];
		}

		return $return;
	}

	/**
	 * Get the timestamp of a file.
	 *
	 * @param string $path
	 *
	 * @return array|false
	 */
	public function getTimestamp( $path ) {
		return $this->fileTable[ $path ]['timestamp'];
	}

	/**
	 * Get filesize.
	 *
	 * @param string $path
	 *
	 * @return int|false
	 */
	public function getSize( $path ) {
		return $this->fileTable[ $path ]['size'];
	}
}