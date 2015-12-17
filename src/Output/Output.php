<?php

namespace kriskbx\wyn\Output;

use kriskbx\wyn\Contracts\Output\CanWriteStream;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Exceptions\WrongArgumentException;

abstract class Output implements OutputContract
{
    protected $config;

    /**
     * Constructor.
     *
     * @param array       $exclude
     * @param bool        $ignore
     * @param bool        $delete
     * @param bool|string $versioning
     * @param bool|string $encrypt
     */
    public function __construct($exclude = [], $ignore = true, $delete = true, $versioning = false, $encrypt = false)
    {
        $this->config['ignore'] = $ignore;
        $this->config['exclude'] = $exclude;
        $this->config['delete'] = $delete;
        $this->config['versioning'] = $versioning;
        $this->config['encrypt'] = $encrypt;
    }

    /**
     * Does the current implementation supports php streaming?
     *
     * @return bool
     */
    public function supportsStreams()
    {
        return ($this instanceof CanWriteStream);
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
