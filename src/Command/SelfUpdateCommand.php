<?php

namespace kriskbx\wyn\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    /**
     * Configure Command.
     */
    public function configure()
    {
        $this->setName('update')
             ->setDescription('Update wyn itself');
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $consoleInput
     * @param OutputInterface $consoleOutput
     *
     * @return int|null|void
     */
    public function execute(InputInterface $consoleInput, OutputInterface $consoleOutput)
    {
        $updater = new Updater();
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('kriskbx/wyn');
        $updater->getStrategy()->setPharName('wyn.phar');
        $updater->getStrategy()->setCurrentLocalVersion($GLOBALS['wynVersion']);
        try {
            $result = $updater->update();
            $result ? exit('Updated!') : exit('No update needed!');
        } catch (\Exception $e) {
            exit('Well, something happened! Either an oopsie or something involving hackers.');
        }
    }
}
