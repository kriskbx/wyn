<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\GlobalConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class EditCommand extends Command
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('edit')
            ->setDescription('Open the global configuration file in your default editor');
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        GlobalConfig::preFlight($output);

        $command = $this->executable().' '.GlobalConfig::getConfigFile();
        $process = new Process($command, realpath(__DIR__.'/../'), array_merge($_SERVER, $_ENV), null, null);

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }

    /**
     * Find the correct executable to run depending on the OS.
     *
     * @return string
     */
    protected function executable()
    {
        if (strpos(strtoupper(PHP_OS), 'WIN') === 0) {
            return 'start';
        } elseif (strpos(strtoupper(PHP_OS), 'DARWIN') === 0) {
            return 'open';
        }

        return 'xdg-open';
    }
}
