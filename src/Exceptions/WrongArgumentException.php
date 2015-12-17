<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class WrongArgumentException extends Exception
{
    /**
     * @var string
     */
    private $argumentName;

    /**
     * Constructor.
     *
     * @param string    $argumentName
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($argumentName, $code = 0, Exception $previousException = null)
    {
        $this->argumentName = $argumentName;

        parent::__construct('The following argument is wrong or invalid: '.$this->getArgumentName(), $code, $previousException);
    }

    /**
     * @return string
     */
    public function getArgumentName()
    {
        return $this->argumentName;
    }
}
