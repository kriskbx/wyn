<?php

namespace kriskbx\wyn\Contracts\Output;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;

interface Output
{
    /**
     * Does the current implementation supports resource streaming?
     *
     * @return bool
     */
    public function supportsStreams();

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path);

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents);

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents);

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path);

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname);

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     *
     * @return array|false
     */
    public function createDir($dirname);

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path);

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false);

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path);

    /**
     * Get filesize.
     *
     * @param string $path
     *
     * @return int|false
     */
    public function getSize($path);

    /**
     * Get config part by the given key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function config($key);

    /**
     * Set something in the config.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setConfig($key, $value);

    /**
     * Apply the given settings to this consumer.
     *
     * @param SyncSettingsContract $settings
     */
    public function applySettings(SyncSettingsContract $settings);
}
