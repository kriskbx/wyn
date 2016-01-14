<?php

namespace kriskbx\wyn\Cron;

use Cron\CronExpression;
use kriskbx\wyn\Contracts\Cron\Cron as CronContract;

abstract class Cron implements CronContract
{
    /**
     * Get next run date.
     *
     * @param string $expression
     *
     * @return int
     */
    protected function getNextRunDate($expression)
    {
        $cron = CronExpression::factory($expression);

        return $cron->getNextRunDate();
    }
}
