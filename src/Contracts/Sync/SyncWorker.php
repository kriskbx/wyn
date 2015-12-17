<?php

namespace kriskbx\wyn\Contracts\Sync;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;

interface SyncWorker
{
    /**
     * Copy a new file from input to output.
     *
     * @param $path
     * @param $type
     *
     * @return string
     */
    public function newFile($path, $type);

    /**
     * Update an existing file, copy the contents from input to output.
     *
     * @param $path
     * @param $type
     *
     * @return string
     */
    public function updateFile($path, $type);

    /**
     * Delete a file at the output side.
     *
     * @param $path
     * @param $type
     *
     * @return string
     */
    public function deleteFile($path, $type);

    /**
     * Set settings property.
     *
     * @param SyncSettingsContract $settings
     *
     * @return $this
     */
    public function settings(SyncSettingsContract $settings);

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
}
