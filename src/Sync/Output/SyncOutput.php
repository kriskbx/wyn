<?php

namespace kriskbx\wyn\Sync\Output;

use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;

abstract class SyncOutput implements SyncOutputContract
{
    /**
     * Total amount of items to process.
     *
     * @var int
     */
    protected $total;

    /**
     * Amount of already processed items.
     *
     * @var int
     */
    protected $processed = 0;

    /*
     * MSG Constants
     */
    const COUNT_NEW_FILES = 'newFiles';
    const COUNT_UPDATE_FILES = 'filesToUpdate';
    const COUNT_DELETE_FILES = 'filesToDelete';

    const ACTION_NEW_FILE = 'newFile';
    const ACTION_UPDATE_FILE = 'updateFile';
    const ACTION_DELETE_FILE = 'deleteFile';

    const VERSIONING_INIT = 'gitInit';
    const VERSIONING_SYNC_TO_LOCAL = 'gitSyncLocal';
    const VERSIONING_COMMIT = 'gitCommit';
    const VERSIONING_SYNC_TO_OUTPUT = 'gitSync';

    const ENC_START_DECRYPTION = 'decryptStart';
    const ENC_DECRYPT_FILE = 'decryptFile';
    const ENC_START_ENCRYPTION = 'encryptStart';
    const ENC_ENCRYPT_FILE = 'encryptFile';
    const ENC_SYNC_STARTUP = 'encryptStartSync';

    const MISC_LINE_BREAK = 'lineBreak';
    const STARTUP_MESSAGE = 'startUp';

    /**
     * Shorthand for write and getText.
     *
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    public function out($name, $data = [])
    {
        return $this->write($this->getText($name, $data));
    }

    /**
     * Set Total.
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
        $this->processed = 0;
    }

    /**
     * Get Total.
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
