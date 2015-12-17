<?php

namespace kriskbx\wyn\Contracts\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
}
