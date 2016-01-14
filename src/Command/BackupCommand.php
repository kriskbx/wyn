<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Application;
use kriskbx\wyn\Contracts\Command\BackupCommand as BackupCommandContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Helper\SyncGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;

abstract class BackupCommand extends Command implements BackupCommandContract {
	use SyncGenerator;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * @param InputInterface $consoleInput
	 * @param OutputInterface $consoleOutput
	 *
	 * @return int|null|void
	 */
	public function execute( InputInterface $consoleInput, OutputInterface $consoleOutput ) {
		$this->input  = $consoleInput;
		$this->output = $consoleOutput;
	}

	/**
	 * Get output.
	 *
	 * @return OutputInterface
	 */
	public function getOutput() {
		return $this->output;
	}

	/**
	 * Get input.
	 *
	 * @return InputInterface
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * @param SyncSettingsContract $settings
	 */
	protected function setTimezone( SyncSettingsContract $settings ) {
		date_default_timezone_set( $settings->timezone() );
	}

	/**
	 * Set default options: skipErrors, exclude, delete.
	 */
	protected function setDefaultOptions() {
		$this->addOption( 'ignoreInput', null, InputOption::VALUE_OPTIONAL, 'Ignore and skip errors on the input side. <info>[Overrides config]</info>' )
		     ->addOption( 'ignoreOutput', null, InputOption::VALUE_OPTIONAL, 'Ignore and skip errors on the output side. <info>[Overrides config]</info>' )
		     ->addOption( 'excludeInput', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude files on the input side, accepts glob. <info>[Overrides config]</info>' )
		     ->addOption( 'excludeOutput', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude files on the output side, accepts glob. <info>[Overrides config]</info>' )
		     ->addOption( 'delete', null, InputOption::VALUE_OPTIONAL, 'Delete files on the output side. <info>[Overrides config]</info>' );
	}

	/**
	 * Get configured outputs.
	 *
	 * @param InputInterface $consoleInput
	 * @param $config
	 * @param $inputName
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getOutputNames( InputInterface $consoleInput, ConfigContract $config, $inputName ) {
		if ( $consoleInput->hasArgument( 'output' ) && ! empty( $consoleInput->getArgument( 'output' ) ) ) {
			return [ $consoleInput->getArgument( 'output' ) ];
		}

		if ( isset( $config->getInput( $inputName )['to'] ) && is_string( $config->getInput( $inputName )['to'] ) ) {
			return [ $config->getInput( $inputName )['to'] ];
		}

		if ( isset( $config->getInput( $inputName )['to'] ) && is_array( $config->getInput( $inputName )['to'] ) ) {
			return $config->getInput( $inputName )['to'];
		}

		throw new \Exception( 'No Output specified and configured.' );
	}

	/**
	 * @param InputInterface $consoleInput
	 * @param OutputInterface $consoleOutput
	 * @param $config
	 * @param $inputName
	 * @param $console
	 *
	 * @return int|void
	 * @throws \Exception
	 */
	public function backup( $inputName, InputInterface $consoleInput, OutputInterface $consoleOutput, ConfigContract $config, SyncOutputContract $console ) {
		// Loop through outputs
		foreach ( $this->getOutputNames( $consoleInput, $config, $inputName ) as $outputName ) {

			// Display started message
			$this->started( $consoleOutput, $inputName, $outputName );

			// Create Sync Settings
			$settings = $this->createSettings( $inputName, $outputName, $consoleInput, $config );
			$this->setTimezone( $settings );

			// Create IO Handler
			$inputHandler  = $this->createInput( $inputName, $config );
			$outputHandler = $this->createOutput( $outputName, $config );

			// Create Sync Application
			$app = new Application();
			$app->create(
				$inputHandler, // input handler
				$outputHandler, // output handler
				$settings, // settings
				$console // sync output
			);

			// Add Middleware
			$this->createVersioningMiddleware( $inputName, $settings, $app );
			$this->createEncryptionMiddleware( $settings, $app );

			// Run Sync
			$app->run();

			// Free some RAM
			unset( $settings, $inputHandler, $outputHandler, $app );
		}
	}

	/**
	 * Display finished message.
	 *
	 * @param OutputInterface $output
	 */
	protected function finished( OutputInterface $output ) {
		$output->writeln( '' );
		$time = round( microtime( true ) - $_SERVER['REQUEST_TIME_FLOAT'], 2 );
		$ram  = round( memory_get_usage( true ) / 1024 / 1024, 2 );
		$output->writeln( "🍺   <info>Finished in $time Seconds. ($ram MB memory used)</info>" );
	}

	/**
	 * Display started message.
	 *
	 * @param OutputInterface $output
	 * @param string $inputName
	 * @param string $outputName
	 */
	protected function started( OutputInterface $output, $inputName, $outputName ) {
		$output->writeln( "<info>Started backup of '$inputName' to '$outputName'</info>" );
	}
}
