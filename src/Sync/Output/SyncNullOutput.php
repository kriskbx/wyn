<?php

namespace kriskbx\wyn\Sync\Output;

/**
 * Class SyncNullOutput.
 */
class SyncNullOutput extends SyncOutput
{
    /**
     * Get the text.
     *
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    public function getText($name, $data = [])
    {
        return '';
    }

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
        return '';
    }

    /**
     * Outputs a string.
     *
     * @param string|array $input
     * @param bool         $newLine
     *
     * @return string
     */
    public function write($input, $newLine = true)
    {
        return '';
    }
}
