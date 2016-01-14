<?php

namespace kriskbx\wyn\Sync;

use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Contracts\Sync\SyncWorker as SyncWorkerContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Errors\SkipErrors;
use kriskbx\wyn\Exceptions\ExceptionHelper;

/**
 * Class SyncWorker.
 */
class SyncWorker implements SyncWorkerContract
{
    use SkipErrors, ExceptionHelper;

    /**
     * Alive latest timestamp.
     *
     * @var int
     */
    protected $aliveTimestamp = 0;

    /**
     * Alive update interval.
     *
     * @var int
     */
    protected $aliveInterval = 10;

    /**
     * Alive file.
     *
     * @var string
     */
    protected $aliveFile = '.wyn-alive';

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
     * Settings.
     *
     * @var SyncSettingsContract
     */
    protected $settings;

    /**
     * SyncWorker Construct.
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
     * Copy a new file.
     *
     * @param $path
     * @param $type
     *
     * @return string
     */
    public function newFile($path, $type)
    {
        $this->alive();

        if ($type == 'dir') {
            $out = $this->createDirectory($path);
        } else {
            if ($this->supportsStreams()) {
                $stream = $this->readStream($path);
                if ($this->isException($stream)) {
                    return $stream;
                }
                $out = $this->writeStream($path, $stream);
            } else {
                $file = $this->read($path);
                if ($this->isException($file)) {
                    return $file;
                }
                $out = $this->write($path, $file);
            }
        }

        return $out;
    }

    /**
     * Update an existing file.
     *
     * @param $path
     * @param $type
     *
     * @return string
     */
    public function updateFile($path, $type)
    {
        $this->alive();

        $out = false;

        if ($type != 'dir') {
            if ($this->supportsStreams()) {
                $stream = $this->readStream($path);
                if ($this->isException($stream)) {
                    return $stream;
                }
                $out = $this->updateStream($path, $stream);
            } else {
                $file = $this->read($path);
                if ($this->isException($file)) {
                    return $file;
                }
                $out = $this->update($path, $file);
            }
        }

        return $out;
    }

    /**
     * Delete a file.
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     */
    public function deleteFile($path, $type)
    {
        $this->alive();

        if ($type == 'dir') {
            $out = $this->catchErrors($this->settings->ignoreOutput(), function () use ($path) {
                $this->output->deleteDir($path);
            });
        } else {
            $out = $this->catchErrors($this->settings->ignoreOutput(), function () use ($path) {
                $this->output->delete($path);
            });
        }

        return $out;
    }

    /**
     * Does this setup support php streaming?
     *
     * @return bool
     */
    protected function supportsStreams()
    {
        return $this->input->supportsStreams() && $this->output->supportsStreams();
    }

    /**
     * Set settings property.
     *
     * @param SyncSettingsContract $settings
     *
     * @return $this
     */
    public function settings(SyncSettingsContract $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Create Directory by the given path.
     *
     * @param string $path
     *
     * @return \Exception|string
     */
    protected function createDirectory($path)
    {
        return $this->catchErrors($this->settings->ignoreOutput(), function () use ($path) {
            return $this->output->createDir($path);
        });
    }

    /**
     * Read Stream by the given path.
     *
     * @param string $path
     *
     * @return \Exception|resource
     */
    protected function readStream($path)
    {
        return $this->catchErrors($this->settings->ignoreInput(), function () use ($path) {
            return $this->input->readStream($path);
        });
    }

    /**
     * Write the given Stream to the given path.
     *
     * @param string   $path
     * @param resource $stream
     *
     * @return \Exception|string
     */
    protected function writeStream($path, $stream)
    {
        return $this->catchErrors($this->settings->ignoreOutput(), function () use ($path, $stream) {
            return $this->output->writeStream($path, $stream);
        });
    }

    /**
     * Read file.
     *
     * @param string $path
     *
     * @return \Exception|string
     */
    protected function read($path)
    {
        return $this->catchErrors($this->settings->ignoreInput(), function () use ($path) {
            return $this->input->read($path);
        });
    }

    /**
     * Write file.
     *
     * @param string $path
     * @param string $file
     *
     * @return \Exception|string
     */
    protected function write($path, $file)
    {
        return $this->catchErrors($this->settings->ignoreOutput(), function () use ($path, $file) {
            return $this->output->write($path, $file);
        });
    }

    /**
     * Update Stream.
     *
     * @param string   $path
     * @param resource $stream
     *
     * @return \Exception|string
     */
    protected function updateStream($path, $stream)
    {
        return $this->catchErrors($this->settings->ignoreOutput(), function () use ($path, $stream) {
            return $this->output->updateStream($path, $stream);
        });
    }

    /**
     * Update.
     *
     * @param string $path
     * @param string $file
     *
     * @return \Exception|string
     */
    protected function update($path, $file)
    {
        return $this->catchErrors($this->settings->ignoreOutput(), function () use ($path, $file) {
            return $this->output->update($path, $file);
        });
    }

    /**
     * This process is alive.
     *
     * @return array|false
     */
    public function alive()
    {
        if ($this->aliveTimestamp >= time() - $this->aliveInterval) {
            return;
        }

        $this->aliveTimestamp = time();

        if ($this->output->has($this->aliveFile)) {
            return $this->output->update($this->aliveFile, time());
        }

        return $this->output->write($this->aliveFile, time());
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

    /**
     * Delete the alive file.
     */
    public function kill()
    {
        if (!$this->output->has($this->aliveFile)) {
            return;
        }

        $this->output->delete($this->aliveFile);
    }

    /**
     * Is another process alive and working on this folder?
     *
     * @return bool
     */
    public function isOtherProcessAlive()
    {
        if (!$this->output->has($this->aliveFile)) {
            return false;
        }

        if ($this->output->read($this->aliveFile) < time() - $this->settings->timeout()) {
            return false;
        }

        return true;
    }
}
