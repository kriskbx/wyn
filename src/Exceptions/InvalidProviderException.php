<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class InvalidProviderException extends Exception
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

        parent::__construct('The provider with the following name is invalid: '.$this->getName(), $code, $previousException);
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
