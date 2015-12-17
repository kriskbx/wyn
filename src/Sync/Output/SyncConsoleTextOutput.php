<?php

namespace kriskbx\wyn\Sync\Output;

use kriskbx\wyn\Exceptions\ExceptionHelper;

/**
 * Class SyncConsoleTextOutput.
 */
class SyncConsoleTextOutput extends SyncConsoleOutput
{
    use ExceptionHelper;

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

        // Log error message if output is an exception
        if (isset($data['output']) && $this->isException($data['output'])) {
            ++$this->processed;

            return '* '.($this->total != 0 ? $this->processed.'/'.$this->total.' ' : '').'<error>'.$data['output']->getMessage().'</error><comment> - skipping...</comment>';
        }

        switch ($name) {
            case SyncOutput::STARTUP_MESSAGE:
                $msg = '<info>Indexing files...</info>';
                break;

            case SyncOutput::COUNT_NEW_FILES:
                $msg = '<info>'.count($data['newFiles']).' new files.</info>';
                break;

            case SyncOutput::COUNT_UPDATE_FILES:
                $msg = '<info>'.count($data['filesToUpdate']).' files will be updated.</info>';
                break;

            case SyncOutput::COUNT_DELETE_FILES:
                $msg = '<info>'.count($data['filesToDelete']).' files will be deleted.</info>';
                break;

            case SyncOutput::ACTION_NEW_FILE:
                $this->processed++;
                $msg = '* '.$this->processed.'/'.$this->total.' <comment>Copied '.$data['arguments']['path'].'</comment>';
                break;

            case SyncOutput::ACTION_UPDATE_FILE:
                $this->processed++;
                $msg = '* '.$this->processed.'/'.$this->total.' <comment>Updated '.$data['arguments']['path'].'</comment>';
                break;

            case SyncOutput::ACTION_DELETE_FILE:
                $this->processed++;
                $msg = '* '.$this->processed.'/'.$this->total.' <comment>Deleted '.$data['arguments']['path'].'</comment>';
                break;

            case SyncOutput::MISC_LINE_BREAK:
                break;

            case SyncOutput::VERSIONING_INIT:
                $msg = '<info>'.$data['gitOutput'].'</info>';
                break;

            case SyncOutput::VERSIONING_SYNC_TO_LOCAL:
                $msg = '<info>Started indexing...</info>';
                break;

            case SyncOutput::VERSIONING_COMMIT:
                $msg = "\n\r".$data['gitOutput'];
                break;

            case SyncOutput::VERSIONING_SYNC_TO_OUTPUT:
                $msg = '<info>Started sync...</info>';
                break;

            case SyncOutput::ENC_START_DECRYPTION:
                $msg = '<info>Started decryption...</info>';
                break;

            case SyncOutput::ENC_DECRYPT_FILE:
                $msg = '* <comment>Decrypted '.$data['file'].'</comment>';
                break;

            case SyncOutput::ENC_START_ENCRYPTION:
                $msg = '<info>Started encryption...</info>';
                break;

            case SyncOutput::ENC_ENCRYPT_FILE:
                $msg = '* <comment>Encrypted '.$data['file'].'</comment>';
                break;
        }

        return $msg;
    }
}
