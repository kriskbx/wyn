<?php

namespace kriskbx\wyn\Contracts\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;

interface BackupCommand
{
    /**
     * Get output.
     *
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * Get input.
     *
     * @return InputInterface
     */
    public function getInput();

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
    public function backup( $inputName, InputInterface $consoleInput, OutputInterface $consoleOutput, ConfigContract $config, SyncOutputContract $console );
}
