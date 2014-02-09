<?php
namespace Codebender\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Jeremy Smereka <ekoedmedia@gmail.com>
 */
class TestProjectCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('codebender:tests:install')
            ->setDescription('Install Test Files')
            ->setHelp(<<<EOT
The <info>codebender:tests:install</info> command moves the test project files to the disk.directory:

  <info>php app/console codebender:tests:install</info>
EOT
            );
    }

    /**
     * @see Command
     *
     * Gets disk.directory parameter and locates files in ProjectBundle.
     * After this uses the Filesystem in Symfony to mirror the folders OR
     * stores files into MongoDB
     *
     * @throws InvalidConfigurationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getContainer()->hasParameter('storagelayer') && $this->getContainer()->getParameter('storagelayer') == "disk") {
            $this->diskInstallTests();
        } else if ($this->getContainer()->hasParameter('storagelayer') && $this->getContainer()->getParameter('storagelayer') == "mongo") {
            $this->mongoInstallTests();
        } else 
            throw new InvalidConfigurationException("Storage layer is not supported or is not set in: parameters.yml");
    }

    /**
     * Install Disk Test Files onto Filesystem
     *
     * Installs disk tests. Filesystem mirrors these, provided disk.directory
     * value is valid.
     *
     * @throws InvalidConfigurationException
     */
    private function diskInstallTests()
    {
        $directory = "";
        if ($this->getContainer()->hasParameter('disk.directory') && $this->getContainer()->getParameter('disk.directory') != null)
            $directory = $this->getContainer()->getParameter('disk.directory');
        else 
            throw new InvalidConfigurationException("Storage layer is disk, but no disk.directory provided in: parameters.yml");

        $path = $this->getApplication()->getKernel()->locateResource('@CodebenderProjectBundle/Resources/files');
        
        $fs = new Filesystem();
        try {
            $fs->mirror($path, $directory);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

    /**
     * Install Disk Test Files into MongoDB
     *
     * Installs disk tests into MongoDB. This is provided the mongodb
     * credentials provided are correct.
     *
     * @throws InvalidConfigurationException
     */
    private function mongoInstallTests()
    {
        ##
        ## To Be Done
        ##
    }

}
