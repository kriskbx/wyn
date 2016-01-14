<?php

namespace kriskbx\wyn\Contracts\Cron;

use kriskbx\wyn\Contracts\Cron\Job as JobContract;

interface Cron {

	/**
	 * Get all scheduled jobs.
	 *
	 * @return JobContract[]
	 */
	public function all();

	/**
	 * Has scheduled job by the given id.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function has( $id );

	/**
	 * Get scheduled job by the given id.
	 *
	 * @param string $id
	 *
	 * @return JobContract
	 */
	public function get( $id );

	/**
	 * Add the given job as cron schedule.
	 *
	 * @param Job $job
	 */
	public function add( JobContract $job );

	/**
	 * Remove a schedule by given id.
	 *
	 * @param string $id
	 */
	public function remove( $id );

	/**
	 * Mark a job as started by the given id.
	 *
	 * @param string $id
	 */
	public function started( $id );

}