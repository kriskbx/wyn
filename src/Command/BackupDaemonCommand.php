<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

class BackupDaemonCommand extends BackupCommand
{
    protected $workerCommand;

    protected $sleep;

    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('backup:daemon')
             ->setDescription('Runs wyn as a daemon that acts as a cron and executes the backup:cron command on a regular basis')
             ->addArgument('config', InputArgument::OPTIONAL, 'Path to the config file.', GlobalConfig::getConfigFile())
             ->addOption('interval', null, InputOption::VALUE_OPTIONAL, 'The interval between running the backup:cron command', 60);

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

        $this->sleep = $consoleInput->getOption('interval');
        $this->workerCommand = $this->buildWorkerCommand();
        $process = $this->makeProcess();

        $consoleOutput->writeln('<info>Daemon started...</info>');

        while (true) {
            $consoleOutput->writeln('<info>Running backup:cron...</info>');

            $process->run(function ($type, $line) {
                $this->output->writeln($line);
            });

            sleep($this->sleep);
        }
    }

    /**
     * Make worker process.
     *
     * @param int $timeout
     *
     * @return Process
     */
    protected function makeProcess($timeout = 60)
    {
        return new Process($this->workerCommand, null, null, null, $timeout);
    }

    /**
     * Build the environment specific worker command.
     *
     * @return string
     */
    protected function buildWorkerCommand()
    {
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder())->find(false));

        if (defined('HHVM_VERSION')) {
            $binary .= ' --php';
        }

        $command = $binary.' '.realpath($_SERVER['SCRIPT_FILENAME']).' backup:cron';

        $command .= ($this->input->getOption('ignoreInput') ? ' --ignoreInput='.$this->input->getOption('ignoreInput') : '');
        $command .= ($this->input->getOption('ignoreOutput') ? ' --ignoreOutput='.$this->input->getOption('ignoreOutput') : '');
        $command .= ($this->input->getOption('excludeInput') ? ' --excludeInput='.$this->input->getOption('excludeInput') : '');
        $command .= ($this->input->getOption('excludeOutput') ? ' --excludeOutput='.$this->input->getOption('excludeOutput') : '');
        $command .= ($this->input->getOption('delete') ? ' --delete='.$this->input->getOption('delete') : '');

        return $command;
    }
}
