<?php

namespace kriskbx\wyn\Sync;

use kriskbx\wyn\Contracts\Sync\SyncManager as SyncManagerContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

/**
 * Class SyncManager.
 */
class SyncManager implements SyncManagerContract
{
    /**
     * Input.
     *
     * @var InputContract
     */
    protected $input;

    /**
     * Output.
     *
     * @var OutputContract
     */
    protected $output;

    /**
     * Input Files.
     *
     * @var array
     */
    protected $inputFiles = [];

    /**
     * Output Files.
     *
     * @var array
     */
    protected $outputFiles = [];

    /**
     * Settings.
     *
     * @var SyncSettingsContract
     */
    protected $settings;

    /**
     * SyncManager Constructor.
     *
     * @param InputContract        $input
     * @param OutputContract       $output
     * @param SyncSettingsContract $settings
     */
    public function __construct(InputContract &$input, OutputContract &$output, SyncSettingsContract &$settings)
    {
        $this->input = $input;
        $this->output = $output;
        $this->settings = $settings;
    }

    /**
     * Read input and output files.
     */
    public function init()
    {
        $this->inputFiles = $this->input->listContents('', true);
        $this->outputFiles = $this->output->listContents('', true);
    }

    /**
     * Get a list of all input files.
     *
     * @return array
     */
    public function getInputFiles()
    {
        return $this->inputFiles;
    }

    /**
     * Get a list of all output files.
     *
     * @return array
     */
    public function getOutputFiles()
    {
        return $this->outputFiles;
    }

    /**
     * Get a list of all files that exists at the input but not at the output side.
     *
     * @return array
     */
    public function getNewFiles()
    {
        $newFiles = [];

        foreach ($this->inputFiles as $file) {
            if (!$this->output->has($file['path'])) {
                $newFiles[] = $file;
            }
        }

        return $newFiles;
    }

    /**
     * Get a list of all files that exists on both sides but differ on the output side.
     *
     * @param bool $checkSize
     * @param bool $checkHash
     *
     * @return array
     */
    public function getFilesToUpdate($checkSize = true, $checkHash = false)
    {
        $filesToUpdate = [];

        foreach ($this->inputFiles as $file) {
            // File exists or it is excluded?
            if (!$this->output->has($file['path']) || $this->exclude($file['path'], $this->settings->excludeOutput())) {
                continue;
            }

            // Compare filesizes
            $fileSize = (!isset($file['size']) || ($checkSize && ($this->output->getSize($file['path']) === $file['size'])));
            // Compare timestamps
            $timeStamp = ($this->output->getTimestamp($file['path']) >= $file['timestamp']);
            // Compare hashes
            $hash = (!$checkHash && (true)); // TODO: integrate checking by hashing the file contents

            if ($fileSize && $timeStamp && $hash) {
                continue;
            }

            $filesToUpdate[] = $file;
        }

        return $filesToUpdate;
    }

    /**
     * Get a list of all files that exist on the output side but not on the input side.
     *
     * @return array
     */
    public function getFilesToDelete()
    {
        $filesToDelete = [];

        foreach ($this->outputFiles as $file) {
            if (!$this->input->has($file['path']) && !$this->exclude($file['path'], $this->settings->excludeOutput())) {
                $filesToDelete[] = $file;
            }
        }

        return $filesToDelete;
    }

    /**
     * Sorts a file list, directories come first.
     *
     * @param array $files
     *
     * @return array
     */
    public function sortDirectoriesUp($files = [])
    {
        $filesOnly = $this->getFiles($files);
        $dirsOnly = $this->getDirs($files);

        return array_merge($dirsOnly, $filesOnly);
    }

    /**
     * Sorts a file list, directories come last.
     *
     * @param array $files
     *
     * @return array
     */
    public function sortDirectoriesDown($files = [])
    {
        $filesOnly = $this->getFiles($files);
        $dirsOnly = $this->getDirs($files);

        return array_merge($filesOnly, $dirsOnly);
    }

    /**
     * Get only files from a file list.
     *
     * @param array $files
     *
     * @return array
     */
    public function getFiles($files = [])
    {
        return array_values(array_filter($files, function ($file) {
            if ($file['type'] == 'dir') {
                return false;
            }

            if ($this->exclude($file['path'], $this->settings->excludeInput())) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Get only directories from a file list.
     *
     * @param array $files
     *
     * @return array
     */
    public function getDirs($files = [])
    {
        return array_values(array_filter($files, function ($file) {
            if ($file['type'] != 'dir') {
                return false;
            }

            if ($this->exclude($file['path'], $this->settings->excludeInput())) {
                return false;
            }

            return false;
        }));
    }

    /**
     * Set settings.
     *
     * @param SyncSettingsContract $settings
     *
     * @return $this
     */
    public function settings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Exclude the given path?
     * TODO: extract (to trait?)
     *
     * @param $path
     * @param $globArray
     *
     * @return bool
     */
    protected function exclude($path, $globArray = [])
    {
        $path = str_replace(["\n", "\r", "\t"], '', $path);

        if (!is_array($globArray)) {
            return false;
        }

        foreach ($globArray as $pattern) {
            if (Glob::match('/'.$path, Path::makeAbsolute($pattern, '/'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set input.
     *
     * @param InputContract $input
     */
    public function setInput(InputContract $input)
    {
        $this->input = $input;
    }

    /**
     * Get input.
     *
     * @return InputContract
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Get output.
     *
     * @return OutputContract
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set output.
     *
     * @param OutputContract $output
     */
    public function setOutput(OutputContract $output)
    {
        $this->output = $output;
    }
}
