<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\GlobalConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('init')
            ->setDescription('Initialize wyn and create global config files/dirs if they don\'t exist');
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
    }
}
