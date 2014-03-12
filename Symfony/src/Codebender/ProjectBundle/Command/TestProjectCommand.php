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
The <info>codebender:tests:install</info> command moves the test project files to the defined directory:

  <info>php app/console codebender:tests:install</info>
EOT
            );
    }

    /**
     * @see Command
     *
     * Gets directory parameter and locates files in ProjectBundle.
     * After this uses the Filesystem in Symfony to mirror the folders OR
     * stores files into MongoDB
     *
     * @throws InvalidConfigurationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->diskInstallTests();
    }

    /**
     * Install Disk Test Files onto Filesystem
     *
     * Installs disk tests. Filesystem mirrors these, provided directory
     * value is valid.
     *
     * @throws InvalidConfigurationException
     */
    private function diskInstallTests()
    {
        $directory = "";
        if ($this->getContainer()->hasParameter('directory') && $this->getContainer()->getParameter('directory') != null)
            $directory = $this->getContainer()->getParameter('directory');
        else 
            throw new InvalidConfigurationException("No directory provided in: parameters.yml");

        $path = $this->getApplication()->getKernel()->locateResource('@CodebenderProjectBundle/Resources/files');
        
        $fs = new Filesystem();
        try {
            $fs->mirror($path, $directory);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

}
