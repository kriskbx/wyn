<?php

namespace kriskbx\wyn\Contracts\Sync;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;

interface SyncManager
{
    /**
     * Read input and output files.
     */
    public function init();

    /**
     * Get a list of all input files.
     *
     * @return array
     */
    public function getInputFiles();

    /**
     * Get a list of all output files.
     *
     * @return array
     */
    public function getOutputFiles();

    /**
     * Get a list of all files that exists at the input but not at the output side.
     *
     * @return array
     */
    public function getNewFiles();

    /**
     * Get a list of all files that exists on both sides but differ on the output side.
     *
     * @param bool $checkSize
     * @param bool $checkHash
     *
     * @return array
     */
    public function getFilesToUpdate($checkSize = true, $checkHash = false);

    /**
     * Get a list of all files that exist on the output side but not on the input side.
     *
     * @return array
     */
    public function getFilesToDelete();

    /**
     * Sorts a file list, directories come first.
     *
     * @param array $files
     *
     * @return array
     */
    public function sortDirectoriesUp($files = []);

    /**
     * Sorts a file list, directories come last.
     *
     * @param array $files
     *
     * @return array
     */
    public function sortDirectoriesDown($files = []);

    /**
     * Get only files from a file list.
     *
     * @param array $files
     *
     * @return array
     */
    public function getFiles($files = []);

    /**
     * Get only directories from a file list.
     *
     * @param array $files
     *
     * @return array
     */
    public function getDirs($files = []);

    /**
     * Set settings.
     *
     * @param SyncSettingsContract $settings
     *
     * @return $this
     */
    public function settings($settings);

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
