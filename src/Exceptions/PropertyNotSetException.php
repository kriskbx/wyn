<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class PropertyNotSetException extends Exception
{
    /**
     * @var string
     */
    private $property;

    /**
     * Constructor.
     *
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($property, $code = 0, Exception $previousException = null)
    {
        parent::__construct('The following required property was not set:'.$this->getProperty(), $code, $previousException);
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
