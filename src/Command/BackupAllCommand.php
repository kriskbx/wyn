<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Application;
use kriskbx\wyn\Config\YamlConfig;
use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use kriskbx\wyn\Sync\SyncSettings;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BackupAllCommand extends BackupCommand {
	/**
	 * Configure Command.
	 */
	public function configure() {
		$this->setName( 'backup:all' )
		     ->setDescription( 'Backups all inputs with configured outputs from the given config file' )
		     ->addArgument( 'config', InputArgument::OPTIONAL, 'Path to the config file.', GlobalConfig::getConfigFile() );

		$this->setDefaultOptions();
	}

	/**
	 * Execute Command.
	 *
	 * @param InputInterface $consoleInput
	 * @param OutputInterface $consoleOutput
	 *
	 * @throws PathNotFoundException
	 * @throws \Exception
	 * @throws \kriskbx\wyn\Exceptions\PropertyNotSetException
	 *
	 * @return int|null|void
	 */
	public function execute( InputInterface $consoleInput, OutputInterface $consoleOutput ) {
		parent::execute( $consoleInput, $consoleOutput );

		// Create config
		$config = new YamlConfig( new Yaml(), $consoleInput->getArgument( 'config' ) );

		// Create console output logger
		$console = $this->createConsoleOutput( $this );

		// Loop through all inputs
		foreach ( $config->getAllInputs() as $inputName ) {
			if ( ! isset( $config->getInput( $inputName )['to'] ) ) {
				continue;
			}

			$this->backup( $inputName, $consoleInput, $consoleOutput, $config, $console );
		}

		// Display the Finished Message
		$this->finished( $consoleOutput );
	}


}
