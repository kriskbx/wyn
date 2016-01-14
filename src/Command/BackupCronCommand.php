<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\YamlConfig;
use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Contracts\Cron\Cron as CronContract;
use kriskbx\wyn\Cron\BackupJob;
use kriskbx\wyn\Cron\FileCron;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BackupCronCommand extends BackupCommand
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('backup:cron')
             ->setDescription('Backups all inputs with matching cron expression from the given config file')
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

        // Create console output logger
        $console = $this->createConsoleOutput($this);

        // Set empty cron object
        $cron = null;

        // Loop through all inputs
        foreach ($config->getAllInputs() as $inputName) {
            if (!isset($config->getInput($inputName)['to']) || !isset($config->getInput($inputName)['cron'])) {
                continue;
            }

            // Create settings and set timezone before the actual backup
            $settings = $this->createSettings($inputName, $this->getOutputNames($consoleInput, $config, $inputName)[0], $consoleInput, $config);
            $this->setTimezone($settings);

            // Create cron object if it isn't already set
            $this->assertCron($cron, $settings);
            // Assert that the job is added to the cron object and up-to-date
            $this->assertJob($config, $inputName, $cron);

            // Start the backup if the job should run
            if ($cron->get($inputName)->shouldRun()) {
                $cron->started($inputName);
                $this->backup($inputName, $consoleInput, $consoleOutput, $config, $console);
            }
        }

        // Display the Finished Message
        $this->finished($consoleOutput);
    }

    /**
     * @param $cron
     * @param $settings
     *
     * @return FileCron
     */
    protected function assertCron(&$cron, $settings)
    {
        if (!$cron) {
            $cron = new FileCron($settings->cronConfig());
        }
    }

    /**
     * @param $config
     * @param $inputName
     * @param $cron
     */
    protected function assertJob(ConfigContract $config, $inputName, CronContract $cron)
    {
        $cronExpression = $config->getInput($inputName)['cron'];

        if (!$cron->has($inputName) || $cron->get($inputName)->getCronExpression() != $cronExpression) {
            $cron->add(new BackupJob($inputName, $cronExpression));
        }
    }
}
