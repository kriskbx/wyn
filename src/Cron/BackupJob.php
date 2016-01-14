<?php

namespace kriskbx\wyn\Cron;

use DateTime;

class BackupJob extends Job
{
    protected $expression;

    protected $nextRun;

    protected $id;

    /**
     * BackupJob constructor.
     *
     * @param string $id
     * @param string $expression
     */
    public function __construct($id, $expression)
    {
        $this->expression = $expression;
        $this->id = $id;
    }

    /**
     * Get cron expression.
     *
     * @return string
     */
    public function getCronExpression()
    {
        return $this->expression;
    }

    /**
     * Set the next run date.
     *
     * @param DateTime $dateTime
     */
    public function setNextRunDate(DateTime $dateTime)
    {
        $this->nextRun = $dateTime;
    }

    /**
     * Should the job run now?
     *
     * @return bool
     */
    public function shouldRun()
    {
        if (time() >= $this->nextRun->getTimestamp()) {
            return true;
        }

        return false;
    }

    /**
     * Get unique identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
