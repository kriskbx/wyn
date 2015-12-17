<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Exceptions\PathNotFoundException;
use League\Flysystem\Adapter\Local;

class LocalOutput extends FlySystemOutput
{
    protected $path;

    /**
     * Constructor.
     *
     * @param string      $path
     * @param array       $exclude
     * @param bool        $ignore
     * @param bool        $delete
     * @param bool|string $versioning
     * @param bool|string $encrypt
     *
     * @throws PathNotFoundException
     */
    public function __construct($path, $exclude = [], $ignore = true, $delete = true, $versioning = false, $encrypt = false)
    {
        parent::__construct($exclude, $ignore, $delete, $versioning, $encrypt);
        $this->path = $path;

        if (!file_exists($this->path)) {
            throw new PathNotFoundException($this->path);
        }

        $this->setFilesystem(new Local($this->path, LOCK_EX, Local::SKIP_LINKS));
    }

    /**
     * Get Path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
