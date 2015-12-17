<?php

namespace kriskbx\wyn\Input;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\CanSkipErrors;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

abstract class FlySystemInput extends Input implements CanSkipErrors, CanReadStream
{
    /**
     * @var
     */
    protected $filesystem;

    /**
     * Setup the filesystem.
     *
     * @param AdapterInterface $adapter
     */
    protected function setFilesystem(AdapterInterface $adapter)
    {
        $this->filesystem = new Filesystem($adapter);
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
        return $this->filesystem->has($path);
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
        return $this->filesystem->read($path);
    }

    /**
     * Retrieves a read-stream for a path.
     *
     * @param string $path
     *
     * @return resource|false path resource or false when on failure
     */
    public function readStream($path)
    {
        return $this->filesystem->readStream($path);
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
        return $this->filesystem->listContents($directory, $recursive);
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
        return $this->filesystem->getTimestamp($path);
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
        return $this->filesystem->getSize($path);
    }
}
