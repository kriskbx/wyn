<?php

namespace kriskbx\wyn\Contracts\Input;

interface CanReadStream
{
    /**
     * Retrieves a read-stream for a path.
     *
     * @param string $path
     *
     * @return resource|false path resource or false when on failure
     */
    public function readStream($path);
}
