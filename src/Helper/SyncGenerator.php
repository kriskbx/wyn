<?php

namespace kriskbx\wyn\Helper;

use Exception;
use kriskbx\wyn\Application;
use kriskbx\wyn\Contracts\Command\BackupCommand;
use kriskbx\wyn\Contracts\Config\Config as ConfigContract;
use kriskbx\wyn\Contracts\Input\Input as InputContract;
use kriskbx\wyn\Contracts\Output\Output as OutputContract;
use kriskbx\wyn\Contracts\Command\BackupCommand as BackupCommandContract;
use kriskbx\wyn\Contracts\Output\Output;
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
     *
     * @throws PathNotFoundException
     * @throws Exception
     *
     * @return OutputContract
     */
    protected function createOutput($name, ConfigContract $config, BackupCommand $command = null)
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

            if (!$helper->ask($input = $command->getInput(), $output = $command->getOutput(), $question)) {
                throw $e;
            }

            mkdir($e->getPath(), 0777, true);
            $output->writeln('');

            return $this->createOutput($input->getArgument('output'), $config);
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
     * @param string      $input
     * @param Output      $outputHandler
     * @param Application $app
     */
    protected function createVersioningMiddleware($input, Output $outputHandler, Application &$app)
    {
        if ($outputHandler->config('versioning') !== 'git') {
            return;
        }

        $app->middleware(new VersioningMiddleware(new GitVersioning($input)));
    }

    /**
     * Create default encryption middleware.
     *
     * @param Output             $outputHandler
     * @param Application        $app
     * @param BackupCommand|null $command
     *
     * @throws PathNotFoundException
     * @throws Exception
     */
    protected function createEncryptionMiddleware(Output $outputHandler, Application &$app, BackupCommand $command = null)
    {
        if ($outputHandler->config('encrypt') === false) {
            return;
        }

        $command = $this->getCommand($command);

        try {
            $encryptionMiddleware = new EncryptionMiddleware($outputHandler->config('encrypt'));
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
            $this->createEncryptionMiddleware($outputHandler, $app, $command);
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
