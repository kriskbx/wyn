<?php

use kriskbx\wyn\Command\BackupAllCommand;
use kriskbx\wyn\Command\BackupCronCommand;
use kriskbx\wyn\Command\BackupDaemonCommand;
use kriskbx\wyn\Command\BackupSingleCommand;
use kriskbx\wyn\Command\DecryptCommand;
use kriskbx\wyn\Command\EditCommand;
use kriskbx\wyn\Command\RollbackUpdateCommand;
use kriskbx\wyn\Command\SelfUpdateCommand;
use kriskbx\wyn\Exceptions\RequirementNotFulfilledException;
use kriskbx\wyn\Helper\RequirementsChecker;
use Symfony\Component\Console\Application;

// Find composer autoload.php
$autoloadGlobal = __DIR__.'/../../../autoload.php';
$autoloadLocal = __DIR__.'/../vendor/autoload.php';

if (file_exists($autoloadGlobal)) {
    require $autoloadGlobal;
    $GLOBALS['autoloadPath'] = $autoloadGlobal;
} elseif (file_exists($autoloadLocal)) {
    require $autoloadLocal;
    $GLOBALS['autoloadPath'] = $autoloadLocal;
} else {
    throw new Exception("Can't find composer autoload.php");
}

// Display that beautiful banner
if ($argc == 1) {
    echo "┬ ┬┬ ┬┌┐┌\n";
    echo "│││└┬┘│││\n";
    echo "└┴┘ ┴ ┘└┘\n\n";
}

// Check for requirements
try {
    RequirementsChecker::check();
} catch (RequirementNotFulfilledException $e) {
    echo "\033[41;37mError: ".$e->getMessage()."\033[0m\n\n";
    die();
}

// Set version
$GLOBALS['wynVersion'] = file_get_contents(__DIR__.'/../VERSION');

// Run the command line application
$app = new Application('wyn', $GLOBALS['wynVersion']);
$app->add(new BackupSingleCommand());
$app->add(new BackupAllCommand());
$app->add(new BackupCronCommand());
$app->add(new BackupDaemonCommand());
$app->add(new EditCommand());
$app->add(new DecryptCommand());
$app->add(new RollbackUpdateCommand());
$app->add(new SelfUpdateCommand());
$app->run();
