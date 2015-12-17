<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Output\CanWriteStream;
use kriskbx\wyn\Contracts\CanSkipErrors;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

abstract class FlySystemOutput extends Output implements CanSkipErrors, CanWriteStream, CanReadStream
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
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents)
    {
        return $this->filesystem->write($path, $contents);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource)
    {
        return $this->filesystem->writeStream($path, $resource);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents)
    {
        return $this->filesystem->update($path, $contents);
    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource)
    {
        $this->filesystem->updateStream($path, $resource);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->filesystem->delete($path);
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return $this->filesystem->deleteDir($dirname);
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     *
     * @return array|false
     */
    public function createDir($dirname)
    {
        return $this->filesystem->createDir($dirname);
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
