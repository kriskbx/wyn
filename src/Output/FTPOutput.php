<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Output\CanWriteStream;
use League\Flysystem\Adapter\Ftp;

class FTPOutput extends FlySystemOutput  implements CanWriteStream, CanReadStream
{
    /**
     * Constructor.
     *
     * @param array  $host
     * @param bool   $username
     * @param bool   $password
     * @param string $root
     * @param int    $port
     * @param bool   $passive
     * @param bool   $ssl
     * @param int    $timeout
     */
    public function __construct(
        $host, $username, $password, $root = '/', $port = 21, $passive = true, $ssl = true, $timeout = 30
    ) {
        $this->setFilesystem(new Ftp([
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'root' => $root,
            'port' => $port,
            'passive' => $passive,
            'ssl' => $ssl,
            'timeout' => $timeout,
        ]));
    }
}
