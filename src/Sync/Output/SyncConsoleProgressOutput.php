<?php

namespace kriskbx\wyn\Sync\Output;

/**
 * Class SyncConsoleProgressOutput.
 */
class SyncConsoleProgressOutput extends SyncConsoleOutput
{
    /**
     * Max length of the progress bar.
     *
     * @var int
     */
    protected $barMaxLength = 0;

    /**
     * Time when the last process started.
     *
     * @var float
     */
    protected $startTime = 0;

    /**
     * Estimated process time.
     *
     * @var int
     */
    protected $estimatedTime = 0;

    /**
     * Last refresh of estimated time.
     *
     * @var int
     */
    protected $lastRefreshEstimated = 0;

    /**
     * Elapsed time.
     *
     * @var int
     */
    protected $elapsed = 0;

    /**
     * Get the text.
     *
     * @param string $name
     * @param array  $data
     *
     * @return string
     */
    public function getText($name, $data = [])
    {
        $msg = '';

        switch ($name) {
            case SyncOutput::MISC_LINE_BREAK:
                break;

            case SyncOutput::ENC_SYNC_STARTUP:
                $msg = '<info>Syncing files...</info>';
                break;

            case SyncOutput::VERSIONING_SYNC_TO_LOCAL:
                $msg = '<info>Started indexing...</info>';
                break;

            case SyncOutput::VERSIONING_SYNC_TO_OUTPUT:
                $msg = '<info>Started sync...</info>';
                break;

            case SyncOutput::ENC_START_DECRYPTION:
                $msg = '<info>Started decryption...</info>';
                break;

            case SyncOutput::ENC_START_ENCRYPTION:
                $msg = '<info>Started encryption...</info>';
                break;

            default:
                $msg = false;
                break;
        }

        if ($name == SyncOutput::ENC_ENCRYPT_FILE || $name == SyncOutput::ENC_DECRYPT_FILE || $name == SyncOutput::ACTION_NEW_FILE || $name == SyncOutput::ACTION_UPDATE_FILE || $name == SyncOutput::ACTION_DELETE_FILE) {
            ++$this->processed;
            $msg = $this->progress($this->total, $this->processed, 30);

            if ($this->processed < $this->total) {
                $msg = 'sl#'.$msg."\r";
            } // Add sl# to force a single line
        }

        return $msg;
    }

    /**
     * Progress bar.
     *
     * @param int $total
     * @param int $processed
     * @param int $barLength
     *
     * @return string
     */
    protected function progress($total, $processed, $barLength)
    {
        $this->calcTimes($total, $processed);

        $current = sprintf('%'.strlen($total).'.d', $processed);
        $percent = number_format(round($processed / $total * 100, 2), 2);

        $progressLength = floor($barLength * $percent / 100);
        $progress = str_repeat('=', $progressLength);

        if ($progressLength < $barLength) {
            $progress .= '>';
            $progress .= str_repeat('-', $barLength - $progressLength - 1);
        }

        $bar = "$current/$total [$progress] $percent%";
        $bar .= '  ['.gmdate('H:i:s', $this->elapsed).' | '.gmdate('H:i:s', round($this->estimatedTime)).']';

        // Add spaces, make sure to overwrite previous bars
        $this->barMaxLength = max(strlen($bar), $this->barMaxLength);
        $bar .= str_repeat(' ', abs($this->barMaxLength - strlen($bar)));

        return $bar;
    }

    /**
     * Set total.
     *
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
        $this->processed = 0;
        $this->barMaxLength = 0;
        $this->startTime = null;
    }

    /**
     * Calculate times.
     *
     * @param int $total
     * @param int $processed
     */
    protected function calcTimes($total, $processed)
    {
        if (!$this->startTime || $processed == 0) {
            $this->startTime = microtime(true);
        }

        $this->elapsed = round(microtime(true) - $this->startTime);

        if ($this->lastRefreshEstimated + 1 <= microtime(true)) {
            $this->estimatedTime = ($this->elapsed / $processed) * $total;
            $this->lastRefreshEstimated = microtime(true);
        }
    }
}
