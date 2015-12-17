<?php

namespace kriskbx\wyn\Input;

use kriskbx\wyn\Contracts\Input\CanReadStream;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Exceptions\WrongArgumentException;

abstract class Input implements InputContract
{
    protected $config;

    /**
     * Constructor.
     *
     * @param array $exclude
     * @param bool  $ignore
     */
    public function __construct($exclude = [], $ignore = true)
    {
        $this->config['ignore'] = $ignore;
        $this->config['exclude'] = $exclude;
    }

    /**
     * Does the current implementation supports php streaming?
     *
     * @return bool
     */
    public function supportsStreams()
    {
        return ($this instanceof CanReadStream);
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws WrongArgumentException
     */
    public function config($key)
    {
        if (!isset($this->config[$key])) {
            throw new WrongArgumentException($key);
        }

        return $this->config[$key];
    }
}
