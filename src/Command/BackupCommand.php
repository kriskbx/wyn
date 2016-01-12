<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Contracts\Command\BackupCommand as BackupCommandContract;
use kriskbx\wyn\Helper\SyncGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BackupCommand extends Command implements BackupCommandContract
{
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Get output.
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Get input.
     *
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set default options: skipErrors, exclude, delete.
     */
    protected function setDefaultOptions()
    {
        $this->addOption('ignoreInput', null, InputOption::VALUE_OPTIONAL, 'Ignore and skip errors on the input side. <info>[Overrides config]</info>')
            ->addOption('ignoreOutput', null, InputOption::VALUE_OPTIONAL, 'Ignore and skip errors on the output side. <info>[Overrides config]</info>')
            ->addOption('excludeInput', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude files on the input side, accepts glob. <info>[Overrides config]</info>')
            ->addOption('excludeOutput', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Exclude files on the output side, accepts glob. <info>[Overrides config]</info>')
            ->addOption('delete', null, InputOption::VALUE_OPTIONAL, 'Delete files on the output side. <info>[Overrides config]</info>');
    }

    /**
     * Display finished message.
     *
     * @param OutputInterface $output
     */
    protected function finished(OutputInterface $output)
    {
        $output->writeln('');
        $time = round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2);
        $ram = round(memory_get_usage(true) / 1024 / 1024, 2);
        $output->writeln("🍺   <info>Finished in $time Seconds. ($ram MB memory used)</info>");
    }
}
