<?php
namespace Codebender\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author Jeremy Smereka <ekoedmedia@gmail.com>
 */
class RunTestProjectCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('codebender:install')
            ->setDescription('Runs Tests using PHPUnit. This will create the database.')
            ->setHelp(<<<EOT
The <info>codebender:tests:run</info> command moves the test project files to the defined directory, creates the database, applies fixtures:

  <info>php app/console codebender:tests:run</info>
EOT
            );
    }

    /**
     * @see Command
     *
     * Gets directory parameter and locates files in ProjectBundle.
     * After this uses the Filesystem in Symfony to mirror the folders
     *
     * @throws InvalidConfigurationException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if ($dialog->askConfirmation($output, '<question>This will create the Database and move the test files. Do you wish to proceed? [Y/N]</question>', false)) {
            $this->diskInstallTests();
            $this->runDatabaseCreate($output);
            $this->runSchemaCreate($output);
            $this->runFixturesLoad($output);
        }
    }

    /**
     * Remove directory and install Disk Test Files onto Filesystem
     *
     * Removes directory then installs disk tests. Filesystem mirrors these,
     * provided directory value is valid.
     *
     * @param OutputInterface $output
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
            $fs->remove($directory);
            $fs->mkdir($directory);
            $fs->mirror($path, $directory);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

    /**
     * Runs Database create
     *
     * @param OutputInterface $output
     */
    private function runDatabaseCreate($output)
    {
        $command = $this->getApplication()->find('doctrine:database:create');

        $arguments = array(
            'command' => 'doctrine:database:create'
        );

        $input = new ArrayInput($arguments);

        $returnCode = $command->run($input, $output);
    }

    /**
     * Runs Schema create
     *
     * @param OutputInterface $output
     */
    private function runSchemaCreate($output)
    {
        $command = $this->getApplication()->find('doctrine:schema:create');

        $arguments = array(
            'command' => 'doctrine:schema:create'
        );

        $input = new ArrayInput($arguments);

        $returnCode = $command->run($input, $output);
    }

    /**
     * Runs Fixtures Load and awaits user input
     *
     * @param OutputInterface $output
     */
    private function runFixturesLoad($output)
    {
        $command = $this->getApplication()->find('doctrine:fixtures:load');

        $arguments = array(
            'command' => 'doctrine:fixtures:load'
        );

        $input = new ArrayInput($arguments);

        $returnCode = $command->run($input, $output);
    }

}
