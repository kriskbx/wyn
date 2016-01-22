<?php

namespace kriskbx\wyn\Sync;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\Sync as SyncContract;
use kriskbx\wyn\Contracts\Sync\SyncManager as SyncManagerContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Contracts\Sync\SyncWorker as SyncWorkerContract;
use kriskbx\wyn\Exceptions\OtherProcessIsRunningException;
use kriskbx\wyn\Sync\Output\SyncOutput;
use ReflectionObject;
use ReflectionParameter;

/**
 * Class Sync.
 */
class Sync implements SyncContract
{
    /**
     * SyncManager.
     *
     * @var SyncManager
     */
    protected $manager;

    /**
     * SyncWorker.
     *
     * @var SyncWorker
     */
    protected $worker;

    /**
     * SyncOutput.
     *
     * @var SyncOutputContract
     */
    protected $outputHelper;

    /**
     * SyncSettings.
     *
     * @var SyncSettingsContract
     */
    protected $settings;

    /**
     * The queue, holding all items to process.
     *
     * @var array
     */
    protected $queue = [];

    /**
     * Sync Constructor.
     *
     * @param SyncManagerContract  $manager
     * @param SyncWorkerContract   $worker
     * @param SyncOutputContract   $outputHelper
     * @param SyncSettingsContract $settings
     */
    public function __construct(SyncManagerContract $manager, SyncWorkerContract $worker, SyncOutputContract &$outputHelper, SyncSettingsContract &$settings)
    {
        $this->manager = $manager;
        $this->worker = $worker;
        $this->outputHelper = $outputHelper;
        $this->settings = $settings;
    }

    /**
     * Initialize the sync process: read the files (new, update, delete) and add them to the queue.
     */
    public function init()
    {
        $this->out(SyncOutput::STARTUP_MESSAGE);

        $this->manager->init();

        $this->addNewFilesToQueue(
            $this->manager->sortDirectoriesUp(
                $this->manager->getNewFiles()
            )
        );

        $this->addFilesToUpdateToQueue(
            $this->manager->getFiles(
                $this->manager->getFilesToUpdate($this->settings->checkFileSize())
            )
        );

        if ($this->settings->delete()) {
            $this->addFilesToDeleteToQueue(
                $this->manager->sortDirectoriesDown(
                    $this->manager->getFilesToDelete()
                )
            );
        }

        if ($this->getFirstFromQueue()) {
            $this->out('lineBreak');
        }

        $this->outputHelper->setTotal($this->total());
    }

    /**
     * Take the first file from the queue,
     * process it and delete it from the queue afterwards.
     *
     * @return bool
     */
    public function run()
    {
        $queueItem = $this->getFirstFromQueue();
        if ($queueItem === false) {
            return false;
        }

        $this->callWorkerFunction($queueItem['function'], $queueItem['file']);
        $this->clearItemFromQueue($queueItem['function']);

        return true;
    }

    /**
     * Call the given function on the SyncWorker.
     *
     * @param string $function
     * @param array  $file
     */
    protected function callWorkerFunction($function, $file)
    {
        $arguments = $this->getMethodArguments($function, $file);
        $output = call_user_func_array([$this->worker, $function], $arguments);

        $this->out($function, ['output' => $output, 'arguments' => $arguments, 'file' => $file]);
    }

    /**
     * Get the first file from the queue.
     * Returns false if there's nothing more.
     *
     * @return array|bool
     */
    protected function getFirstFromQueue()
    {
        $function = $this->getFirstFunctionFromQueue();

        if ($function === false) {
            return false;
        }

        return [
            'function' => $function,
            'file' => $this->queue[ $function ][0],
        ];
    }

    /**
     * Get the first function from the queue.
     * Returns false if there's nothing more.
     *
     * @return string|bool
     */
    protected function getFirstFunctionFromQueue()
    {
        if (count($this->queue) <= 0) {
            return false;
        }

        $function = array_keys($this->queue)[0];

        if (count($this->queue[ $function ]) <= 0) {
            unset($this->queue[ $function ]);

            return $this->getFirstFunctionFromQueue();
        }

        return $function;
    }

    /**
     * Set the queue.
     *
     * @param string $function
     * @param array  $files
     *
     * @return array
     */
    protected function setQueue($function, $files)
    {
        return $this->queue[ $function ] = $files;
    }

    /**
     * Delete the given item from the queue.
     * Reset the queue keys.
     *
     * @param string $function
     * @param int    $index
     */
    protected function clearItemFromQueue($function, $index = 0)
    {
        unset($this->queue[ $function ][ $index ]);
        $this->queue[ $function ] = array_values($this->queue[ $function ]);
    }

    /**
     * Outputs something to the console.
     *
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    protected function out($name, $data = [])
    {
        return $this->outputHelper->out($name, $data);
    }

    /**
     * Get params from the given worker method.
     *
     * @param string $function
     *
     * @return ReflectionParameter[]
     */
    protected function getMethodParams($function)
    {
        $reflection = new ReflectionObject($this->worker);

        return $reflection->getMethod($function)->getParameters();
    }

    /**
     * Get the arguments for the given worker method by a file.
     *
     * @param string $function
     * @param array  $file
     *
     * @return array
     */
    protected function getMethodArguments($function, $file)
    {
        $params = $this->getMethodParams($function);

        $arguments = [];
        foreach ($params as $param) {
            foreach ($file as $property => $value) {
                if ($property == $param->getName()) {
                    $arguments[ $property ] = $value;
                }
            }
        }

        return $arguments;
    }

    /**
     * Add new files to the queue.
     *
     * @param array $files
     */
    public function addNewFilesToQueue($files)
    {
        $this->setQueue('newFile', $files);
        $this->out(SyncOutput::COUNT_NEW_FILES, ['newFiles' => $files]);
    }

    /**
     * Add files to the queue that need an update.
     *
     * @param array $files
     */
    public function addFilesToUpdateToQueue($files)
    {
        $this->setQueue('updateFile', $files);
        $this->out(SyncOutput::COUNT_UPDATE_FILES, ['filesToUpdate' => $files]);
    }

    /**
     * Add files to the queue that should be deleted.
     *
     * @param array $files
     */
    public function addFilesToDeleteToQueue($files)
    {
        $this->setQueue('deleteFile', $files);
        $this->out(SyncOutput::COUNT_DELETE_FILES, ['filesToDelete' => $files]);
    }

    /**
     * Get queue length.
     *
     * @param string $function
     *
     * @return int
     */
    public function queueLength($function)
    {
        if (!isset($this->queue[ $function ])) {
            return 0;
        }

        return count($this->queue[ $function ]);
    }

    /**
     * Get total queue length.
     *
     * @return int
     */
    public function total()
    {
        return $this->queueLength('newFile') + $this->queueLength('updateFile') + $this->queueLength('deleteFile');
    }

    /**
     * Process the complete queue.
     */
    public function sync()
    {
        if ($this->worker->isOtherProcessAlive()) {
            throw new OtherProcessIsRunningException();
        }

        while ($this->run()) {
        }

        $this->worker->kill();
    }

    /**
     * Get SyncManager.
     *
     * @return SyncManagerContract
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set SyncManager.
     *
     * @param SyncManagerContract $manager
     */
    public function setManager(SyncManagerContract $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get SyncWorker.
     *
     * @return SyncWorkerContract
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Set SyncWorker.
     *
     * @param SyncWorkerContract $worker
     */
    public function setWorker(SyncWorkerContract $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Get SyncSettings.
     *
     * @return SyncSettingsContract
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set SyncSettings.
     *
     * @param SyncSettingsContract $settings
     */
    public function setSettings(SyncSettingsContract $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Set input.
     *
     * @param InputContract $input
     */
    public function setInput(InputContract $input)
    {
        $this->worker->setInput($input);
        $this->manager->setInput($input);
        $this->settings->setInput($input);
    }

    /**
     * Get input.
     *
     * @return InputContract
     */
    public function getInput()
    {
        return $this->worker->getInput();
    }

    /**
     * Set output.
     *
     * @param OutputContract $output
     */
    public function setOutput(OutputContract $output)
    {
        $this->worker->setOutput($output);
        $this->manager->setOutput($output);
        $this->settings->setOutput($output);
    }

    /**
     * Get output.
     *
     * @return OutputContract
     */
    public function getOutput()
    {
        return $this->worker->getOutput();
    }

    /**
     * Get output helper.
     *
     * @return SyncOutputContract
     */
    public function getOutputHelper()
    {
        return $this->outputHelper;
    }
}
