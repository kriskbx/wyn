<?php

namespace kriskbx\wyn\Middleware;

use kriskbx\wyn\Contracts\Middleware\Middleware as MiddlewareContract;

abstract class Middleware implements MiddlewareContract
{
    /**
     * Priority, lower number == higher priority.
     *
     * @var int
     */
    protected $priority = 50;

    /**
     * Get priority.
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }
}
