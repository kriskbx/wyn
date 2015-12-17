<?php

namespace kriskbx\wyn\Output;

class NullOutput extends Output
{
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
        return [];
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
        return [];
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
        return true;
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
        return true;
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
        return [];
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
        return false;
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
        return [];
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
        return false;
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
        return 0;
    }
}
