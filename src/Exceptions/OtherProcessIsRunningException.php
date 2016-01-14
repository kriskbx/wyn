<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class OtherProcessIsRunningException extends Exception
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($code = 0, Exception $previousException = null)
    {
        parent::__construct('Another process of wyn is running and using this output at the moment.', $code, $previousException);
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
