<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class JobNotFoundException extends Exception
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string    $name
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($name, $code = 0, Exception $previousException = null)
    {
        $this->name = $name;

        parent::__construct('The job with the following identifier could not be found: '.$this->getName(), $code, $previousException);
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
