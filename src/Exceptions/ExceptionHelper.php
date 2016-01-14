<?php

namespace kriskbx\wyn\Exceptions;

use Exception;

trait ExceptionHelper
{
    /**
     * Is the given input an exception?
     *
     * @param mixed $input
     *
     * @return bool
     */
    protected function isException($input)
    {
        if (!is_object($input)) {
            return false;
        }

        return $input instanceof Exception;
    }
}
