<?php

namespace Codebender\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Codebender\UserBundle\Entity\User;

/* Load User Data
 * 
 * Provides default User Data for CodeBender.cc
 */
class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data into Users Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     * Creates a Single User for Testing
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $testUserOne = $userManager->createUser();
        $testUserOne->setUsername('tester');
        $testUserOne->setEmail('tester@codebender.cc');
        $testUserOne->setEnabled(1);
        $testUserOne->setFirstname('myfirstname');
        $testUserOne->setLastname('mylastname');
        $testUserOne->setTwitter('codebender_cc');
        $testUserOne->addRole('ROLE_ADMIN');
        $testUserOne->setPlainPassword('testerPASS');
        $testUserOne->setPreregistrationDate(new \DateTime("@1352246400"));
        $testUserOne->setRegistrationDate(new \DateTime("@1387821667"));
        $testUserOne->setLastEdit(new \DateTime("@1389892016"));
        $testUserOne->setLastCompile(new \DateTime("@1390228951"));
        $testUserOne->setLastFlash(new \DateTime("@1390228951"));
        $testUserOne->setLastCloning(new \DateTime("@1389352726"));
        $testUserOne->setActualLastLogin(new \DateTime("@1390228951"));
        $testUserOne->setLastWalkthroughDate(new \DateTime("@1389608243"));
        $userManager->updateUser($testUserOne);

        $testUserTwo = $userManager->createUser();
        $testUserTwo->setUsername('testacc');
        $testUserTwo->setEmail('testacc@codebender.cc');
        $testUserTwo->setEnabled(1);
        $testUserTwo->setPlainPassword('testaccPWD');
        $testUserTwo->setPreregistrationDate(new \DateTime("@1352246400"));
        $testUserTwo->setRegistrationDate(new \DateTime("@1387821667"));
        $testUserTwo->setLastEdit(new \DateTime("@1389892016"));
        $testUserTwo->setLastCompile(new \DateTime("@1390228951"));
        $testUserTwo->setLastFlash(new \DateTime("@1390228951"));
        $testUserTwo->setLastCloning(new \DateTime("@1389352726"));
        $testUserTwo->setActualLastLogin(new \DateTime("@1390228951"));
        $testUserTwo->setLastWalkthroughDate(new \DateTime("@1389608243"));
        $userManager->updateUser($testUserTwo);

        $testUserThree = $userManager->createUser();
        $testUserThree->setUsername('optionTester');
        $testUserThree->setEmail('optiontester@codebender.cc');
        $testUserThree->setEnabled(1);
        $testUserThree->setFirstname('John');
        $testUserThree->setLastname('Doe');
        $testUserThree->setTwitter('codebender_cc');
        $testUserThree->setPlainPassword('optionTesterPASS');
        $testUserThree->setRegistrationDate(new \DateTime('2013-05-13'));
        $userManager->updateUser($testUserThree);

        $testUserFour = $userManager->createUser();
        $testUserFour->setUsername('eulaTester');
        $testUserFour->setEmail('eulatester@codebender.cc');
        $testUserFour->setEnabled(1);
        $testUserFour->setFirstname('Eula');
        $testUserFour->setLastname('Tester');
        $testUserFour->setPlainPassword('eulaTesterPASS');
        $testUserFour->setRegistrationDate(new \DateTime('2013-05-13'));
        $testUserFour->setEula(false);

        $userManager->updateUser($testUserFour);

        $initialUser = $userManager->createUser();
        $initialUser->setUsername('userAdmin');
        $initialUser->setEmail('userAdmin@codebender.cc');
        $initialUser->setEnabled(1);
        $initialUser->setPlainPassword('userPassword');
        $userManager->updateUser($initialUser);

        // Setup References for next Fixtures
        $this->addReference('admin-user', $testUserOne);
        $this->addReference('reg-user', $testUserTwo);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
