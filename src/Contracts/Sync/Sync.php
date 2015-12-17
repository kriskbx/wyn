<?php

namespace kriskbx\wyn\Contracts\Sync;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\SyncManager as SyncManagerContract;
use kriskbx\wyn\Contracts\Sync\SyncWorker as SyncWorkerContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;

interface Sync
{
    /**
     * Sync Constructor.
     *
     * @param SyncManagerContract  $manager
     * @param SyncWorkerContract   $worker
     * @param SyncOutputContract   $outputHelper
     * @param SyncSettingsContract $settings
     */
    public function __construct(SyncManagerContract $manager, SyncWorkerContract $worker, SyncOutputContract &$outputHelper, SyncSettingsContract &$settings);

    /**
     * Initialize the sync process: read the files (new, update, delete) and add them to the queue.
     */
    public function init();

    /**
     * Take the first file from the queue, process it and delete it from the queue afterwards.
     *
     * @return bool
     */
    public function run();

    /**
     * Process the complete queue.
     */
    public function sync();

    /**
     * Add new files to the queue.
     *
     * @param array $files
     */
    public function addNewFilesToQueue($files);

    /**
     * Add files to the queue that need an update.
     *
     * @param array $files
     */
    public function addFilesToUpdateToQueue($files);

    /**
     * Add files to the queue that should be deleted.
     *
     * @param array $files
     */
    public function addFilesToDeleteToQueue($files);

    /**
     * Get queue length.
     *
     * @param string $function
     *
     * @return int
     */
    public function queueLength($function);

    /**
     * Get total queue length.
     *
     * @return int
     */
    public function total();

    /**
     * Get SyncManager.
     *
     * @return SyncManagerContract
     */
    public function getManager();

    /**
     * Set SyncManager.
     *
     * @param SyncManagerContract $manager
     */
    public function setManager(SyncManagerContract $manager);

    /**
     * Get SyncWorker.
     *
     * @return SyncWorkerContract
     */
    public function getWorker();

    /**
     * Set SyncWorker.
     *
     * @param SyncWorkerContract $worker
     */
    public function setWorker(SyncWorkerContract $worker);

    /**
     * Get SyncSettings.
     *
     * @return SyncSettingsContract
     */
    public function getSettings();

    /**
     * Set SyncSettings.
     *
     * @param SyncSettingsContract $settings
     */
    public function setSettings(SyncSettingsContract $settings);

    /**
     * Set input.
     *
     * @param InputContract $input
     */
    public function setInput(InputContract $input);

    /**
     * Get input.
     *
     * @return InputContract
     */
    public function getInput();

    /**
     * Get output.
     *
     * @return OutputContract
     */
    public function getOutput();

    /**
     * Set output.
     *
     * @param OutputContract $output
     */
    public function setOutput(OutputContract $output);

    /**
     * Get output helper.
     *
     * @return SyncOutputContract
     */
    public function getOutputHelper();
}
