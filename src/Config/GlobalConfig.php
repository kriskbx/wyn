<?php

namespace kriskbx\wyn\Config;

use Symfony\Component\Console\Output\OutputInterface;

class GlobalConfig
{
    protected static $gitDir = 'versioning';

    protected static $configFile = 'global.yml';

    /**
     * @return string
     */
    public static function getConfigDir()
    {
        return $_SERVER['HOME'].DIRECTORY_SEPARATOR.'.wyn'.DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public static function getConfigFile()
    {
        return self::getConfigDir().static::$configFile;
    }

    /**
     * @return string
     */
    public static function getGitDir()
    {
        return self::getConfigDir().static::$gitDir;
    }

    /**
     * @param OutputInterface $output
     */
    public static function preFlight(OutputInterface $output)
    {
        $didSomething = false;

        if (!file_exists($dir = self::getConfigDir())) {
            mkdir($dir);
            $output->writeln('* <comment>Created global config directory: '.$dir.'</comment>');
            $didSomething = true;
        }

        if (!file_exists($dir = self::getGitDir())) {
            mkdir($dir);
            $output->writeln('* <comment>Created global versioning directory: '.$dir.'</comment>');
            $didSomething = true;
        }

        if (!file_exists($file = self::getConfigFile())) {
            touch($file);
            $output->writeln('* <comment>Created global config file: '.$file.'</comment>');
            $didSomething = true;
        }

        if (fileperms($file) != 600) {
            chmod($file, 0600);
            $output->writeln('* <comment>Set global config permissions to: 0600</comment>');
            $didSomething = true;
        }

        if ($didSomething) {
            $output->writeln('');
        }
    }
}
