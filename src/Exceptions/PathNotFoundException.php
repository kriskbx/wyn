<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class PathNotFoundException extends Exception
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Constructor.
     *
     * @param string    $path
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($path, $code = 0, Exception $previousException = null)
    {
        $this->path = $path;

        parent::__construct('Path not found: '.$this->getPath(), $code, $previousException);
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
