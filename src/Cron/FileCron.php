<?php


namespace kriskbx\wyn\Cron;


use kriskbx\wyn\Contracts\Cron\Job;
use kriskbx\wyn\Contracts\Cron\Job as JobContract;
use kriskbx\wyn\Exceptions\JobNotFoundException;

class FileCron extends Cron {

	protected $jobFile = 'cronjobs';

	protected $jobDirectory;

	protected $jobs = [ ];

	/**
	 * FileCron constructor.
	 *
	 * @param string $jobDirectory
	 */
	public function __construct( $jobDirectory ) {
		$this->jobDirectory = $jobDirectory;

		$this->read();
	}

	/**
	 * Has scheduled job by the given id.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function has( $id ) {
		return isset( $this->jobs[ $id ] );
	}

	/**
	 * Get all scheduled jobs.
	 *
	 * @return JobContract[]
	 */
	public function all() {
		return $this->jobs;
	}

	/**
	 * Get scheduled job by the given id.
	 *
	 * @param string $id
	 *
	 * @return JobContract
	 *
	 * @throws JobNotFoundException
	 */
	public function get( $id ) {
		$this->assertJob( $id );

		return $this->jobs[ $id ];
	}

	/**
	 * Add the given job as cron schedule.
	 *
	 * @param Job $job
	 */
	public function add( JobContract $job ) {
		$job->setNextRunDate( $this->getNextRunDate( $job->getCronExpression() ) );

		$this->jobs[ $job->getId() ] = $job;

		$this->update();
	}

	/**
	 * Remove a schedule by given id.
	 *
	 * @param string $id
	 */
	public function remove( $id ) {
		$this->assertJob( $id );

		unset( $this->jobs[ $id ] );

		$this->update();
	}

	/**
	 * Mark a job as started by the given id.
	 *
	 * @param string $id
	 */
	public function started( $id ) {
		$this->assertJob( $id );

		$this->jobs[ $id ]->setNextRunDate( $this->getNextRunDate( $this->jobs[ $id ]->getCronExpression() ) );

		$this->update();
	}

	/**
	 *
	 */
	protected function read() {
		$this->jobs = unserialize( file_get_contents( $this->getCronFile() ) );
	}

	/**
	 * Write everything to the json file.
	 */
	protected function update() {
		file_put_contents( $this->getCronFile(), serialize( $this->jobs ) );
	}

	/**
	 * Get path of the json file "storage".
	 *
	 * @return string
	 */
	protected function getCronFile() {
		$file = $this->jobDirectory . $this->jobFile;

		$this->assertFile( $file );

		return $file;
	}

	/**
	 * @param $file
	 */
	protected function assertFile( $file ) {
		if ( ! file_exists( $file ) ) {
			touch( $file );
			file_put_contents( $file, serialize( [ ] ) );
		}
	}

	/**
	 * @param $id
	 *
	 * @throws JobNotFoundException
	 */
	protected function assertJob( $id ) {
		if ( ! isset( $this->jobs[ $id ] ) ) {
			throw new JobNotFoundException( $id );
		}
	}
}