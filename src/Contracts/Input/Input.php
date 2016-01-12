<?php

namespace kriskbx\wyn\Contracts\Input;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;

interface Input
{
    /**
     * Does the current implementation supports resource streaming?
     *
     * @return bool
     */
    public function supportsStreams();

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path);

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path);

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
