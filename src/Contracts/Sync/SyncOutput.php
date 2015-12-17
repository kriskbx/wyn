<?php

namespace kriskbx\wyn\Contracts\Sync;

interface SyncOutput
{
    /**
     * Outputs a string.
     *
     * @param string|array $input
     * @param bool         $newLine
     *
     * @return string
     */
    public function write($input, $newLine = true);

    /**
     * Get the text.
     *
     * @param $name
     * @param array $data
     *
     * @return string
     */
    public function getText($name, $data = []);

    /**
     * Shorthand for write and getText.
     *
     * @param $name
     * @param array $data
     *
     * @return string
     */
    public function out($name, $data = []);

    /**
     * Set total amount of items to process.
     *
     * @param int $total
     */
    public function setTotal($total);

    /**
     * Get total amount of items to process.
     *
     * @return int
     */
    public function getTotal();
}
