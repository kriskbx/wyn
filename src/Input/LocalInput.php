<?php

namespace kriskbx\wyn\Input;

use kriskbx\wyn\Exceptions\PathNotFoundException;
use League\Flysystem\Adapter\Local;

class LocalInput extends FlySystemInput
{
    protected $path;

    /**
     * Constructor.
     *
     * @param array $path
     *
     * @throws PathNotFoundException
     */
    public function __construct($path)
    {
        $this->path = $path;

        if (!file_exists($this->path)) {
            throw new PathNotFoundException($this->path);
        }

        $this->setFilesystem(new Local($this->path, LOCK_EX, Local::SKIP_LINKS));
    }
}
