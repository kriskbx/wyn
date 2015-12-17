<?php

namespace kriskbx\wyn\Contracts\Output;

interface CanWriteStream
{
    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource);

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource);
}
