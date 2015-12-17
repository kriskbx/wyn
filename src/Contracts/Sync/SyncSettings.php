<?php

namespace kriskbx\wyn\Contracts\Sync;

use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;

interface SyncSettings
{
    /**
     * Set settings.
     */
    public function init();

    /**
     * Get skipInputErrors.
     *
     * @return bool
     */
    public function skipInputErrors();

    /**
     * Get skipOutputErrors.
     *
     * @return bool
     */
    public function skipOutputErrors();

    /**
     * Get delete.
     *
     * @return bool
     */
    public function delete();

    /**
     * Get excludeInput.
     *
     * @return array|null
     */
    public function excludeInput();

    /**
     * Get excludeOutput.
     *
     * @return array|null
     */
    public function excludeOutput();

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
     * Set skipInputErrors.
     *
     * @param bool $skipInputErrors
     */
    public function setSkipInputErrors($skipInputErrors);

    /**
     * Set SkipOutputErrors.
     *
     * @param bool $skipOutputErrors
     */
    public function setSkipOutputErrors($skipOutputErrors);

    /**
     * Set Delete.
     *
     * @param bool $delete
     */
    public function setDelete($delete);

    /**
     * Set excludeInput.
     *
     * @param array $excludeInput
     */
    public function setExcludeInput($excludeInput);

    /**
     * Set ExcludeOutput.
     *
     * @param array $excludeOutput
     */
    public function setExcludeOutput($excludeOutput);
}
