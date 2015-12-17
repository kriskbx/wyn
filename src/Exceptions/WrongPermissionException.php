<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

class WrongPermissionException extends Exception
{
    /**
     * @var string
     */
    protected $perm;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Exception
     */
    protected $should;

    /**
     * Constructor.
     *
     * @param string    $name
     * @param string    $perm
     * @param string    $should
     * @param int       $code
     * @param Exception $previousException
     */
    public function __construct($name, $perm, $should, $code = 0, Exception $previousException = null)
    {
        $this->name = $name;
        $this->perm = $perm;
        $this->should = $should;

        parent::__construct('The permissions ('.$this->getPerm().') for the given file is too open (should be '.$this->getShould().'): '.$this->getName(), $code, $previousException);
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

    /**
     * @return string
     */
    public function getPerm()
    {
        return $this->perm;
    }

    /**
     * @return Exception
     */
    public function getShould()
    {
        return $this->should;
    }
}
