<?php

namespace kriskbx\wyn\Helper;

use kriskbx\wyn\Application;
use kriskbx\wyn\Contracts\Command\BackupCommand;
use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Command\BackupCommand as BackupCommandContract;
use kriskbx\wyn\Contracts\Sync\SyncSettings as SyncSettingsContract;
use kriskbx\wyn\Sync\SyncSettings;
use Symfony\Component\Console\Input\InputInterface;
use kriskbx\wyn\Contracts\Sync\SyncOutput;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use kriskbx\wyn\Middleware\EncryptionMiddleware;
use kriskbx\wyn\Middleware\VersioningMiddleware;
use kriskbx\wyn\Sync\Output\SyncConsoleProgressOutput;
use kriskbx\wyn\Sync\Output\SyncConsoleTextOutput;
use kriskbx\wyn\Versioning\GitVersioning;
use phpSec\Crypt\Rand;
use ReflectionObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

trait SyncGenerator
{
    /**
     * Create a new SyncSettings object. Overwrite settings from config with command line option.
     *
     * @param $inputName
     * @param $outputName
     * @param InputInterface $input
     * @param ConfigContract $config
     *
     * @return SyncSettings
     */
    protected function createSettings($inputName, $outputName, InputInterface $input, ConfigContract $config)
    {
        $inputOptions = $config->getInput($inputName);
        $outputOptions = $config->getOutput($outputName);

        $settings = new SyncSettings();

        foreach ($settings as $key => $setting) {
            if ($input->hasOption($key) && $value = $input->getOption($key)) {
                call_user_func_array([$settings, 'set'.ucfirst($key)], [$value]);
            } elseif (isset($inputOptions[ $key ]) && in_array($key, SyncSettings::getInputBaseOptions())) {
                call_user_func_array([$settings, 'set'.ucfirst($key)], [$inputOptions[ $key ]]);
            } elseif (isset($inputOptions[ str_replace('Input', '', $key) ]) && in_array(str_replace('Input', '', $key), SyncSettings::getInputBaseOptions())) {
                call_user_func_array([
                    $settings,
                    'set'.ucfirst($key),
                ], [$inputOptions[ str_replace('Input', '', $key) ]]);
            } elseif (isset($outputOptions[ $key ]) && in_array($key, SyncSettings::getOutputBaseOptions())) {
                call_user_func_array([$settings, 'set'.ucfirst($key)], [$outputOptions[ $key ]]);
            } elseif (isset($outputOptions[ str_replace('Output', '', $key) ]) && in_array(str_replace('Output', '', $key), SyncSettings::getOutputBaseOptions())) {
                call_user_func_array([
                    $settings,
                    'set'.ucfirst($key),
                ], [$outputOptions[ str_replace('Output', '', $key) ]]);
            } elseif ($config->hasOption($key) && in_array($key, SyncSettings::getGeneralBaseOptions())) {
                call_user_func_array([$settings, 'set'.ucfirst($key)], [$config->getOption($key)]);
            }
        }

        return $settings;
    }

    /**
     * @param string         $name
     * @param ConfigContract $config
     *
     * @return InputContract
     */
    protected function createInput($name, ConfigContract $config)
    {
        return (new IOGenerator($name, $config->getInput($name)))->validate()->make();
    }

    /**
     * @param string         $name
     * @param ConfigContract $config
     * @param BackupCommand  $command
     * @param bool           $noInteraction
     *
     * @return OutputContract
     *
     * @throws PathNotFoundException
     */
    protected function createOutput($name, ConfigContract $config, BackupCommand $command = null, $noInteraction = false)
    {
        $command = $this->getCommand($command);

        try {
            return (new IOGenerator($name, $config->getOutput($name), 'output'))->validate()->make();
        } catch (PathNotFoundException $e) {
            // If we have access to the actual command object we can ask the
            // user if he or she wants to create the missing output directory
            // and finally create the directory for the user.
            if (!$command) {
                throw $e;
            }

            $helper = $command->getHelper('question');
            $question = new ConfirmationQuestion("<comment>The directory '".$e->getPath()."' doesn't exist. Create it?</comment> <info>[y|n]</info>\n", false);

            if (!$helper->ask($input = $command->getInput(), $output = $command->getOutput(), $question) && !$noInteraction) {
                throw $e;
            }

            mkdir($e->getPath(), 0777, true);
            $output->writeln('');

            return $this->createOutput($name, $config);
        }
    }

    /**
     * Get default console output.
     *
     * @param BackupCommand $command
     *
     * @return SyncOutput
     */
    protected function createConsoleOutput(BackupCommand $command)
    {
        if ($command->getOutput()->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $console = new SyncConsoleTextOutput($command->getOutput());
        } else {
            $console = new SyncConsoleProgressOutput($command->getOutput());
        }

        return $console;
    }

    /**
     * Get default versioning middleware.
     *
     * @param string               $input
     * @param SyncSettingsContract $settings
     * @param Application          $app
     */
    protected function createVersioningMiddleware($input, SyncSettingsContract $settings, Application &$app)
    {
        if ($settings->versioning() !== 'git') {
            return;
        }

        $app->middleware(new VersioningMiddleware(new GitVersioning($input)));
    }

    /**
     * Create default encryption middleware.
     *
     * @param SyncSettingsContract $settings
     * @param Application          $app
     * @param BackupCommand|null   $command
     *
     * @throws PathNotFoundException
     */
    protected function createEncryptionMiddleware(SyncSettingsContract $settings, Application &$app, BackupCommand $command = null)
    {
        if ($settings->encrypt() === false) {
            return;
        }

        $command = $this->getCommand($command);

        try {
            $encryptionMiddleware = new EncryptionMiddleware($settings->encrypt());
            $app->middleware($encryptionMiddleware);
        } catch (PathNotFoundException $e) {
            // If we have access to the actual command object we can ask the user
            // if a new random encryption key should be generated and do this task
            // for him or her.
            if (!$command) {
                throw $e;
            }

            $helper = $command->getHelper('question');
            $question = new ConfirmationQuestion("<comment>The encryption-key '".$e->getPath()."' doesn't exist. Create a new random one?</comment> <info>[y|n]</info>\n", false);
            if (!$helper->ask($input = $command->getInput(), $output = $command->getOutput(), $question)) {
                throw $e;
            }

            touch($e->getPath());
            chmod($e->getPath(), 0600);
            file_put_contents($e->getPath(), (new Rand())->bytes(32));

            $output->writeln('');
            $this->createEncryptionMiddleware($settings, $app, $command);
        }
    }

    /**
     * Get command object.
     *
     * @param null $command
     *
     * @return $this|BackupCommandContract|null
     */
    protected function getCommand($command = null)
    {
        if ($command) {
            return $command;
        }

        $reflection = new ReflectionObject($this);
        if ($reflection->implementsInterface(BackupCommandContract::class)) {
            return $this;
        }

        return;
    }
}
