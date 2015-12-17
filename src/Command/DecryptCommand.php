<?php

namespace kriskbx\wyn\Command;

use kriskbx\wyn\Config\GlobalConfig;
use kriskbx\wyn\Config\YamlConfig;
use kriskbx\wyn\Exceptions\PathNotFoundException;
use kriskbx\wyn\Helper\SyncGenerator;
use kriskbx\wyn\Middleware\EncryptionMiddleware;
use kriskbx\wyn\Output\LocalOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DecryptCommand extends BackupCommand
{
    use SyncGenerator;

    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('decrypt')
            ->setDescription('Decrypt the data from an encrypted output')
            ->addArgument('output', InputArgument::REQUIRED, 'Name of the configured output.')
            ->addArgument('target', InputArgument::REQUIRED, 'Path to save the decrypted data to.')
            ->addArgument('config', InputArgument::OPTIONAL, 'Path to the config file.', GlobalConfig::getConfigFile());
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws PathNotFoundException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        if (!file_exists($input->getArgument('target')) || !is_dir($input->getArgument('target'))) {
            throw new PathNotFoundException($input->getArgument('target'));
        }

        // Get Config
        $config = new YamlConfig(new Yaml(), $input->getArgument('config'));

        // Create IO Handler
        $inputHandler = $this->createOutput($input->getArgument('output'), $config);
        $outputHandler = new LocalOutput($input->getArgument('target'));

        // Console output
        $console = $this->createConsoleOutput($this);

        // Crypto
        $crypto = new EncryptionMiddleware($inputHandler->config('encrypt'));
        $crypto->decrypt($inputHandler, $outputHandler, $console);

        // Finished
        $this->finished($output);
    }
}
