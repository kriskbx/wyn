<?php

namespace kriskbx\wyn\Contracts\Cron;

use DateTime;

interface Job
{
    /**
     * Get unique identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get cron expression.
     *
     * @return string
     */
    public function getCronExpression();

    /**
     * Set the next run date.
     *
     * @param DateTime $dateTime
     */
    public function setNextRunDate(DateTime $dateTime);

    /**
     * Should the job run now?
     *
     * @return bool
     */
    public function shouldRun();
}
