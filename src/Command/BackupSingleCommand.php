<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\YamlConfig;
use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BackupSingleCommand extends BackupCommand
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('backup:single')
             ->setDescription('Backups a single input to a single output from the given config file')
             ->addArgument('input', InputArgument::REQUIRED, 'Name of the configured input.')
             ->addArgument('output', InputArgument::OPTIONAL, 'Name of the configured output. Required if no output is configured.')
             ->addArgument('config', InputArgument::OPTIONAL, 'Path to the config file.', GlobalConfig::getConfigFile());

        $this->setDefaultOptions();
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $consoleInput
     * @param OutputInterface $consoleOutput
     *
     * @throws PathNotFoundException
     * @throws \Exception
     * @throws \kriskbx\wyn\Exceptions\PropertyNotSetException
     *
     * @return int|null|void
     */
    public function execute(InputInterface $consoleInput, OutputInterface $consoleOutput)
    {
        parent::execute($consoleInput, $consoleOutput);

        // Create Config
        $config = new YamlConfig(new Yaml(), $consoleInput->getArgument('config'));

        // Run backup
        $this->backup($consoleInput->getArgument('input'), $consoleInput, $consoleOutput, $config, $this->createConsoleOutput($this));

        // Display the Finished Message
        $this->finished($consoleOutput);
    }
}
