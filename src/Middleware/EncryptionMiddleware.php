<?php

namespace kriskbx\wyn\Middleware;

use InvalidArgumentException;
use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Sync\Sync as SyncContract;
use kriskbx\wyn\Contracts\Sync\SyncOutput as SyncOutputContract;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use kriskbx\wyn\Exceptions\WrongPermissionException;
use kriskbx\wyn\Sync\Output\SyncOutput;
use phpSec\Core;
use phpSec\Crypt\Crypto;
use phpSec\Exception\GeneralSecurityException;

final class EncryptionMiddleware extends Middleware
{
    /**
     * Priority.
     *
     * @var int
     */
    protected $priority = 10;

    /**
     * Encryption key.
     *
     * @var string
     */
    private $key;

    /**
     * @var Crypto
     */
    private $crypto;

    /**
     * Constructor.
     *
     * @param string|bool $key
     *
     * @throws PathNotFoundException
     * @throws WrongPermissionException
     */
    public function __construct($key)
    {
        $this->crypto = new Crypto(new Core());

        if ($key === true) {
            $keyFile = GlobalConfig::getConfigDir().'encryption_key';

            if (!file_exists($keyFile) || !is_file($keyFile) || !is_readable($keyFile)) {
                throw new PathNotFoundException($keyFile);
            }

            if (fileperms($keyFile) != 0600) {
                chmod($keyFile, 0600);
            }

            $this->key = file_get_contents($keyFile);

            return;
        }

        if (file_exists($key) && is_file($key) && is_readable($key)) {
            if (fileperms($key) != 0600) {
                throw new WrongPermissionException($key, fileperms($key), '0600');
            }

            $this->key = file_get_contents($key);

            return;
        }

        if (is_string($key)) {
            $this->key = $key;

            return;
        }

        throw new InvalidArgumentException('Invalid encrypt value.');
    }

    /**
     * Before process.
     *
     * @param SyncContract $sync
     */
    public function beforeProcess(SyncContract &$sync)
    {
        // Get output
        $console = $sync->getOutputHelper();
        $output = $sync->getOutput();

        // Display some stuff
        $console->out(SyncOutput::ENC_START_DECRYPTION);
        $console->out(SyncOutput::MISC_LINE_BREAK);

        // Decrypt
        $this->decrypt($output, $output, $console);

        $console->out(SyncOutput::MISC_LINE_BREAK);
        $console->out(SyncOutput::MISC_LINE_BREAK);

        $console->out(SyncOutput::ENC_SYNC_STARTUP);
    }

    /**
     * After process.
     *
     * @param SyncContract $sync
     */
    public function afterProcess(SyncContract &$sync)
    {
        // Get output & console
        $console = $sync->getOutputHelper();
        $output = $sync->getOutput();

        // Display some stuff
        $console->out(SyncOutput::MISC_LINE_BREAK);
        $console->out(SyncOutput::ENC_START_ENCRYPTION);
        $console->out(SyncOutput::MISC_LINE_BREAK);

        // Encrypt
        $this->encrypt($output, $output, $console);

        $console->out(SyncOutput::MISC_LINE_BREAK);
    }

    /**
     * Encrypt the given output.
     *
     * @param OutputContract          $input
     * @param OutputContract          $output
     * @param SyncOutputContract|null $console
     *
     * @throws \phpSec\Exception\InvalidAlgorithmParameterException
     * @throws \phpSec\Exception\InvalidKeySpecException
     */
    public function encrypt(OutputContract $input, OutputContract $output, SyncOutputContract $console = null)
    {
        $files = $input->listContents('', true);
        if ($console) {
            $console->setTotal(count($files));
        }

        foreach ($files as $file) {
            if ($file['type'] == 'dir') {
                if ($console) {
                    $console->out(SyncOutput::ENC_ENCRYPT_FILE, ['file' => $file['path']]);
                }
                continue;
            }

            $fileContents = $this->crypto->encrypt($input->read($file['path']), $this->key);
            $output->update($file['path'], $fileContents);

            unset($fileContents);

            if ($console) {
                $console->out(SyncOutput::ENC_ENCRYPT_FILE, ['file' => $file['path']]);
            }
        }
    }

    /**
     * Decrypt the given output.
     *
     * @param OutputContract     $input
     * @param OutputContract     $output
     * @param SyncOutputContract $console
     */
    public function decrypt(OutputContract $input, OutputContract $output, SyncOutputContract $console = null)
    {
        $files = $input->listContents('', true);
        if ($console) {
            $console->setTotal(count($files));
        }

        foreach ($files as $file) {
            if ($file['type'] == 'dir') {
                if ($console) {
                    $console->out(SyncOutput::ENC_ENCRYPT_FILE, ['file' => $file['path']]);
                }
                continue;
            }

            try {
                $fileContents = $this->crypto->decrypt($input->read($file['path']), $this->key);
                if ($output->has($file['path'])) {
                    $output->update($file['path'], $fileContents);
                } else {
                    $output->write($file['path'], $fileContents);
                }

                unset($fileContents);

                if ($console) {
                    $console->out(SyncOutput::ENC_DECRYPT_FILE, ['file' => $file['path']]);
                }
            } catch (GeneralSecurityException $e) {
                if ($console) {
                    $console->out(SyncOutput::ENC_DECRYPT_FILE, ['file' => $file['path'], 'output' => $e]);
                }
            }
        }
    }
}
