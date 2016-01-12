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

class BackupAllCommand extends BackupCommand
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('all')
             ->setDescription('Backups all inputs with configured outputs from the given config file')
             ->addArgument('config', InputArgument::OPTIONAL, 'Path to the config file.', GlobalConfig::getConfigFile());

        $this->setDefaultOptions();
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws PathNotFoundException
     * @throws \Exception
     * @throws \kriskbx\wyn\Exceptions\PropertyNotSetException
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Get Config
        $config = new YamlConfig(new Yaml(), $input->getArgument('config'));
//
//        // Create IO Handler
//        $inputHandler = $this->createInput($input->getArgument('input'), $config);
//        $outputHandler = $this->createOutput($input->getArgument('output'), $config);
//
//        // Console output
//        $console = $this->createConsoleOutput($this);
//
//        // Create Sync Application
//        $app = new Application();
//        $settings = new SyncSettings(
//            $input->getOption('excludeInput'), // exclude input
//            $input->getOption('excludeOutput'), // exclude output
//            $input->getOption('ignoreInput'), // skip input errors
//            $input->getOption('ignoreOutput'), // skip output errors
//            $input->getOption('delete') // delete
//        );
//        $app->create(
//            $inputHandler, // input handler
//            $outputHandler, // output handler
//            $settings, // settings
//            $console // sync output
//        );
//
//        // Add Middleware
//        $this->createVersioningMiddleware($input->getArgument('input'), $outputHandler, $app);
//        $this->createEncryptionMiddleware($outputHandler, $app);
//
//        // Run Sync
//        $app->run();

        // Display the Finished Message
        $this->finished($output);
    }
}
