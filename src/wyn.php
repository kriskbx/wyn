<?php

use kriskbx\wyn\Command\BackupAllCommand;
use kriskbx\wyn\Command\BackupCronCommand;
use kriskbx\wyn\Command\BackupSingleCommand;
use kriskbx\wyn\Command\DecryptCommand;
use kriskbx\wyn\Command\EditCommand;
use kriskbx\wyn\Exceptions\RequirementNotFulfilledException;
use kriskbx\wyn\Helper\RequirementsChecker;
use Symfony\Component\Console\Application;

$autoloadGlobal = __DIR__ . '/../../../autoload.php';
$autoloadLocal  = __DIR__ . '/../vendor/autoload.php';

if ( file_exists( $autoloadGlobal ) ) {
	require $autoloadGlobal;
	$GLOBALS['autoloadPath'] = $autoloadGlobal;
} elseif ( file_exists( $autoloadLocal ) ) {
	require $autoloadLocal;
	$GLOBALS['autoloadPath'] = $autoloadLocal;
} else {
	throw new Exception( "Can't find composer autoload.php" );
}

if ( $argc == 1 ) {
	echo "┬ ┬┬ ┬┌┐┌\n";
	echo "│││└┬┘│││\n";
	echo "└┴┘ ┴ ┘└┘\n\n";
}

$app = new Application( 'wyn', file_get_contents( __DIR__ . '/../VERSION' ) );
$app->add( new BackupSingleCommand );
$app->add( new BackupAllCommand );
$app->add( new BackupCronCommand );
$app->add( new EditCommand );
$app->add( new DecryptCommand );
$app->run();