<?php

namespace kriskbx\wyn\Input;

use Ifsnop\Mysqldump\Mysqldump;
use kriskbx\wyn\Helper\RequirementsChecker;
use PDO;

class MySQLInput extends Input
{
    protected $host;

    protected $user;

    protected $password;

    protected $database;

    protected $port;

    protected $socket;

    protected $pdo;

    protected $fileTable;

    protected $dumper;

    protected $dumperData = [];

    protected $extension = '.sql';

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
     * @param bool   $noData
     * @param bool   $addDropTable
     * @param bool   $singleTransaction
     * @param bool   $lockTables
     * @param bool   $addLocks
     * @param bool   $extendedInsert
     * @param bool   $disableKeys
     * @param bool   $noCreateInfo
     * @param bool   $skipTriggers
     * @param bool   $addDropTrigger
     * @param bool   $routines
     * @param bool   $hexBlob
     * @param bool   $skipTzUtc
     * @param bool   $noAutocommit
     * @param string $defaultCharacterSet
     * @param bool   $skipComments
     * @param bool   $skipDumpDate
     */
    public function __construct(
        $host = null, $user, $password, $database = null, $port = 3306, $socket = null,
        $compress = 'None', $noData = false, $addDropTable = false, $singleTransaction = true,
        $lockTables = false, $addLocks = true, $extendedInsert = true,
        $disableKeys = true, $noCreateInfo = false, $skipTriggers = false, $addDropTrigger = true,
        $routines = false, $hexBlob = true, $skipTzUtc = false, $noAutocommit = true, $defaultCharacterSet = 'utf8',
        $skipComments = false, $skipDumpDate = false
    ) {
        RequirementsChecker::check('PDO');

        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->socket = $socket;

        if (stristr($compress, 'gzip')) {
            $this->extension = '.gz';
        } elseif (stristr($compress, 'bzip')) {
            $this->extension = '.bzip';
        }

        $this->dumperData = [
            'compress' => $compress,
            'no-data' => $noData,
            'add-drop-table' => $addDropTable,
            'single-transaction' => $singleTransaction,
            'lock-tables' => $lockTables,
            'add-locks' => $addLocks,
            'extended-insert' => $extendedInsert,
            'disable-keys' => $disableKeys,
            'no-create-info' => $noCreateInfo,
            'skip-triggers' => $skipTriggers,
            'add-drop-trigger' => $addDropTrigger,
            'routines' => $routines,
            'hex-blob' => $hexBlob,
            'skip-tz-utc' => $skipTzUtc,
            'no-autocommit' => $noAutocommit,
            'default-character-set' => $defaultCharacterSet,
            'skip-comments' => $skipComments,
            'skip-dump-date' => $skipDumpDate,
        ];

        $this->database = $this->getDatabase($database);
        $this->setFileTable();
    }

    /**
     * Get DSN.
     *
     * @param string $database
     *
     * @return string
     */
    protected function getDsn($database = '')
    {
        if ($database) {
            $database = ';dbname='.$database;
        }

        if ($this->socket) {
            return 'mysql:unix_socket='.$this->socket.$database;
        }

        return 'mysql:host='.$this->host.';port='.$this->port.$database;
    }

    /**
     * Connect.
     */
    protected function connect()
    {
        if (!$this->pdo) {
            $this->pdo = new PDO($this->getDsn(), $this->user, $this->password);
        }
    }

    /**
     * Get an array of available databases.
     *
     * @return array
     */
    protected function databases()
    {
        $databases = [];

        $this->connect();
        $query = $this->pdo->query('SHOW DATABASES');

        while (($database = $query->fetchColumn(0)) !== false) {
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
    protected function getDatabase($database = null)
    {
        if (!$database) {
            return $this->databases();
        }

        if (is_array($database)) {
            return $database;
        }

        return [$database];
    }

    /**
     * @param $database
     *
     * @return int
     */
    protected function getDatabaseSize($database)
    {
        if (in_array('information_schema', $this->database)) {
            $this->pdo->query('USE information_schema');
            $query = $this->pdo->query(
                "SELECT Sum(data_length + index_length) \"size\"
				FROM information_schema.tables
				WHERE table_schema = \"$database\""
            );

            return $query->fetchColumn(0);
        }

        return 0;
    }

    /**
     * @param $database
     *
     * @return int
     */
    protected function getDatabaseLastModified($database)
    {
        if (in_array('information_schema', $this->database)) {
            $this->pdo->query('USE information_schema');
            $countQuery = $this->pdo->query(
                "SELECT COUNT(*) \"count\" FROM information_schema.tables WHERE table_schema = \"$database\""
            );

            // Return 1 to prevent empty databases to be updated every time.
            if ($countQuery->fetch()['count'] == 0) {
                return 1;
            }

            $query = $this->pdo->query(
                "SELECT MAX(update_time) \"update\", MAX(create_time) \"create\"
				FROM information_schema.tables
				WHERE table_schema = \"$database\""
            );

            $result = $query->fetch();
            $timestamp = max(strtotime($result['create']), strtotime($result['update']));

            return $timestamp ? $timestamp : time();
        }

        return time();
    }

    /**
     * Set the "table" of "files".
     */
    protected function setFileTable()
    {
        foreach ($this->database as $database) {
            $this->fileTable[ $database.$this->extension ] = [
                'database' => $database,
                'size' => $this->getDatabaseSize($database),
                'timestamp' => $this->getDatabaseLastModified($database),
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
    public function has($path)
    {
        return array_key_exists($path, $this->fileTable);
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        $database = str_ireplace($this->extension, '', $path);

        $this->dumper = new Mysqldump(
            $this->getDsn($database),
            $this->user,
            $this->password,
            $this->dumperData
        );

        $file = sys_get_temp_dir().'/wyn_mysql_dump_'.$database.'_'.uniqid().$this->extension;
        $this->dumper->start($file);

        $data = file_get_contents($file);
        unlink($file);

        return $data;
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $return = [];

        foreach ($this->fileTable as $file => $fileData) {
            $return[] = [
                'type' => 'file',
                'path' => $file,
                'timestamp' => $fileData['timestamp'],
                'size' => $fileData['size'],
                'dirname' => '',
                'basename' => $file,
                'extension' => $this->extension,
                'filename' => str_ireplace($this->extension, '', $file),
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
    public function getTimestamp($path)
    {
        return $this->fileTable[ $path ]['timestamp'];
    }

    /**
     * Get filesize.
     *
     * @param string $path
     *
     * @return int|false
     */
    public function getSize($path)
    {
        return $this->fileTable[ $path ]['size'];
    }
}
