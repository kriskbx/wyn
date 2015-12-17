<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class ConfigFileNotFoundException extends Exception
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

        parent::__construct('Config file not found at path: '.$this->getPath(), $code, $previousException);
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
