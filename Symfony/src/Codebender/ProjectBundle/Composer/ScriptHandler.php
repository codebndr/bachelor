<?php

namespace Codebender\ProjectBundle\Composer;

use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;

/**
 * @author Jeremy Smereka <ekoedmedia@gmail.com>
 *
 * Most of code copied and merged from:
 *     vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Composer/ScriptHandler.php
 */
class ScriptHandler
{

    /**
     * Used by Composer to install the Test Projects
     */
    public static function installTestProjects(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not build bootstrap file.'.PHP_EOL;

            return;
        }

        $php = escapeshellarg(self::getPhp());
        $cmd = escapeshellarg('codebender:tests:install');
        $appDir = escapeshellarg($appDir);

        error_log($php."\n".$cmd."\n".$appDir);
        $process = new Process($php.' '.$appDir.'/console '.$cmd, null, null, null, 300);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('An error occurred when mirroring the test sketches.');
        }
    }

    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
        ), $event->getComposer()->getPackage()->getExtra());

        return $options;
    }

    protected static function getPhp()
    {
        $phpFinder = new PhpExecutableFinder;
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}