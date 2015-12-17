<?php

namespace kriskbx\wyn\Sync;

use Exception;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;

/**
 * Class SyncSettings.
 */
class SyncSettings implements SyncSettingsContract
{
    /**
     * Skip errors on the input side?
     *
     * @var bool
     */
    protected $skipInputErrors;

    /**
     *  Skip errors on the output side?
     *
     * @var bool
     */
    protected $skipOutputErrors;

    /**
     * Delete files?
     *
     * @var bool
     */
    protected $delete;

    /**
     * Exclude on the input side.
     *
     * @var array
     */
    protected $excludeInput;

    /**
     * Exclude on the output side.
     *
     * @var array
     */
    protected $excludeOutput;

    /**
     * Input.
     *
     * @var InputContract
     */
    protected $inputHandler;

    /**
     * Output.
     *
     * @var OutputContract
     */
    protected $outputHandler;

    /**
     * SyncSettings constructor.
     * Favors the given parameters from the Constructor over the ones defined in the config and stored in input- and
     * outputHandler.
     *
     * @param InputContract  $inputHandler
     * @param OutputContract $outputHandler
     * @param array          $excludeInput
     * @param array          $excludeOutput
     * @param bool           $skipInputErrors
     * @param bool           $skipOutputErrors
     * @param bool           $delete
     */
    public function __construct($excludeInput = null, $excludeOutput = null, $skipInputErrors = null, $skipOutputErrors = null, $delete = null, InputContract &$inputHandler = null, OutputContract &$outputHandler = null)
    {
        $this->inputHandler = $inputHandler;
        $this->outputHandler = $outputHandler;
        $this->excludeInput = $excludeInput;
        $this->excludeOutput = $excludeOutput;
        $this->skipInputErrors = $skipInputErrors;
        $this->skipOutputErrors = $skipOutputErrors;
        $this->delete = $delete;
    }

/**
 * Set settings.
 */
public function init()
{
    if (!$this->inputHandler || !$this->outputHandler) {
        throw new Exception('Input- and OutputHandler not set in SyncSettings');
    }

    $this->skipInputErrors = (
    !is_null($this->skipInputErrors)
        ? $this->parseBoolean($this->skipInputErrors)
        : $this->inputHandler->config('ignore')
    );

    $this->skipOutputErrors = (
    !is_null($this->skipOutputErrors)
        ? $this->parseBoolean($this->skipOutputErrors)
        : $this->outputHandler->config('ignore')
    );

    $this->delete = (
    !is_null($this->delete)
        ? $this->parseBoolean($this->delete)
        : $this->outputHandler->config('delete')
    );

    $this->excludeInput = (
    !is_null($this->excludeInput)
        ? $this->excludeInput
        : $this->inputHandler->config('exclude')
    );

    $this->excludeOutput = (
    !is_null($this->excludeOutput)
        ? $this->excludeOutput
        : $this->outputHandler->config('exclude')
    );
}

/**
 * Parse boolean.
 *
 * @param mixed $input
 *
 * @return bool
 */
protected function parseBoolean($input)
{
    return ($input === true || $input === 1 || $input === '1' || $input === 'true');
}

/**
 * Get skipInputErrors.
 *
 * @return bool
 */
public function skipInputErrors()
{
    return $this->skipInputErrors;
}

/**
 * Get skipOutputErrors.
 *
 * @return bool
 */
public function skipOutputErrors()
{
    return $this->skipOutputErrors;
}

/**
 * Get delete.
 *
 * @return bool
 */
public function delete()
{
    return $this->delete;
}

/**
 * Get excludeInput.
 *
 * @return array|null
 */
public function excludeInput()
{
    return $this->excludeInput;
}

/**
 * Get excludeOutput.
 *
 * @return array|null
 */
public function excludeOutput()
{
    return $this->excludeOutput;
}

/**
 * Set input.
 *
 * @param InputContract $input
 */
public function setInput(InputContract $input)
{
    $this->inputHandler = $input;
}

/**
 * Get input.
 *
 * @return InputContract
 */
public function getInput()
{
    return $this->inputHandler;
}

/**
 * Get output.
 *
 * @return OutputContract
 */
public function getOutput()
{
    return $this->outputHandler;
}

/**
 * Set output.
 *
 * @param OutputContract $output
 */
public function setOutput(OutputContract $output)
{
    $this->outputHandler = $output;
}

/**
 * @param bool $skipInputErrors
 */
public function setSkipInputErrors($skipInputErrors)
{
    $this->skipInputErrors = $skipInputErrors;
}

/**
 * @param bool $skipOutputErrors
 */
public function setSkipOutputErrors($skipOutputErrors)
{
    $this->skipOutputErrors = $skipOutputErrors;
}

/**
 * @param bool $delete
 */
public function setDelete($delete)
{
    $this->delete = $delete;
}

/**
 * @param array $excludeInput
 */
public function setExcludeInput($excludeInput)
{
    $this->excludeInput = $excludeInput;
}

/**
 * @param array $excludeOutput
 */
public function setExcludeOutput($excludeOutput)
{
    $this->excludeOutput = $excludeOutput;
}
}
