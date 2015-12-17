<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class MissingArgumentException extends Exception
{
    /**
     * @var string
     */
    private $argumentName;

    /**
     * @var int
     */
    private $providerName;

    /**
     * Constructor.
     *
     * @param string    $argumentName
     * @param int       $providerName
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($argumentName, $providerName, $code = 0, Exception $previousException = null)
    {
        $this->argumentName = $argumentName;
        $this->providerName = $providerName;

        parent::__construct('The following required argument was not found in the config for '.$this->getProviderName().': '.$this->getArgumentName(), $code, $previousException);
    }

    /**
     * @return string
     */
    public function getArgumentName()
    {
        return $this->argumentName;
    }

    /**
     * @return int
     */
    public function getProviderName()
    {
        return $this->providerName;
    }
}
