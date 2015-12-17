<?php

namespace kriskbx\wyn\Sync\Output;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncConsoleOutput.
 */
abstract class SyncConsoleOutput extends SyncOutput
{
    /**
     * Holds the output object.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * SyncConsoleOutput Constructor.
     *
     * @param OutputInterface $output
     * @param int             $total
     */
    public function __construct(OutputInterface $output, $total = 0)
    {
        $this->output = $output;
        $this->total = $total;
    }

    /**
     * Outputs a string on the console.
     *
     * @param string|array $input
     * @param bool         $newLine
     *
     * @return string
     */
    public function write($input, $newLine = true)
    {
        if ($input === false) {
            return false;
        }

        if (is_string($input)) {
            return $this->outputWrite($input, $newLine);
        }

        if (!is_array($input)) {
            return false;
        }

        foreach ($input as $msg) {
            return $this->outputWrite($msg, $newLine);
        }
    }

    /**
     * Output the given string to the console.
     *
     * @param string $msg
     * @param bool   $newLine can be overridden within $msg by the strings nl# (newLine) and sl# (singleLine)
     *
     * @return mixed
     */
    protected function outputWrite($msg, $newLine = true)
    {
        if (strstr($msg, 'nl#')) {
            $msg = str_replace('nl#', '', $msg);
            $newLine = true;
        }
        if (strstr($msg, 'sl#')) {
            $msg = str_replace('sl#', '', $msg);
            $newLine = false;
        }

        if ($newLine) {
            return $this->output->writeln($msg);
        } else {
            return $this->output->write($msg);
        }
    }
}
