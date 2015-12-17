<?php

namespace kriskbx\wyn\Errors;

use Closure;
use Exception;
use kriskbx\wyn\Contracts\CanSkipErrors;

trait SkipErrors
{
    /**
     * Can/Should the given object skip errors?
     *
     * @param object $object
     *
     * @return bool
     */
    protected function canSkipErrors($object)
    {
        return ($object instanceof CanSkipErrors);
    }

    /**
     * Handle errors.
     *
     * @param bool    $skip
     * @param Closure $callable
     *
     * @return Exception|mixed
     */
    protected function catchErrors($skip, Closure $callable)
    {
        if ($skip) {
            try {
                return call_user_func($callable);
            } catch (Exception $exception) {
                return $exception;
            }
        } else {
            return call_user_func($callable);
        }
    }
}
