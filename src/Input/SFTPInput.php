<?php

namespace kriskbx\wyn\Input;

use League\Flysystem\Sftp\SftpAdapter;

class SFTPInput extends FlySystemInput
{
    /**
     * Constructor.
     *
     * @param string $path
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $privateKey
     * @param int    $timeout
     * @param int    $port
     * @param array  $exclude
     * @param bool   $ignore
     */
    public function __construct($path, $host, $username, $password = null, $privateKey = null, $timeout = 10, $port = 21, $exclude = [], $ignore = true)
    {
        parent::__construct($exclude, $ignore);

        $adapter = new SftpAdapter([
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $password,
            'privateKey' => $privateKey,
            'root' => $path,
            'timeout' => $timeout,
            'directoryPerm' => 0755,
        ]);

        $this->setFilesystem($adapter);
    }
}
