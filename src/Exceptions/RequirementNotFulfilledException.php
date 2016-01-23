<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class RequirementNotFulfilledException extends Exception
{
    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($message, $code = 0, Exception $previousException = null)
    {
        parent::__construct($message, $code, $previousException);
    }
}
