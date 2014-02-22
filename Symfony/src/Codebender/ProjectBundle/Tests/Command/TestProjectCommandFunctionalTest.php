<?php

namespace Codebender\ProjectBundle\Tests\Command;

use Codebender\ProjectBundle\Command\TestProjectCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Jeremy Smereka <ekoedmedia@gmail.com>
 *
 * Unit Tests for Tests Install Script for Command Line
 */
class TestProjectCommandFunctionalTest extends WebTestCase
{

    /**
     * Functional Test for mirroring of Test Files
     *
     * Tests that installation of files on disk or mongo
     * is successful.
     *
     * @test
     */
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new TestProjectCommand());

        $command = $application->find('codebender:tests:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
            )
        );
        

        $testFiles = $kernel->locateResource('@CodebenderProjectBundle/Resources/files');

        $this->diskExecute($testFiles, $kernel);
        ## More Tests can be added here if needed when more storage types are added, if ever.
    }

    /**
     * Disk Execution
     *
     * Finds files in the resource directory, then checks if for each file
     * its path exists in the disk.directory.
     */
    protected function diskExecute($testFilesLocation, $kernel)
    {
        $finder = new Finder();
        $fs = new Filesystem();

        $directory = $kernel->getContainer()->getParameter('directory');
        $finder->files()->in($testFilesLocation);

        foreach ($finder as $file) {
            $filePath = str_replace($testFilesLocation, "", $file->getPath()."/".$file->getFilename());

            try {
                $this->assertTrue($fs->exists($directory.$filePath));
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while checking if mirror was successful: ".$e->getPath();
            }
        }
    }

}