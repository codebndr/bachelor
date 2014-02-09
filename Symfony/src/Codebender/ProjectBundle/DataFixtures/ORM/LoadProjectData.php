<?php

namespace Codebender\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Codebender\ProjectBundle\Entity\Project;

/* Load Project Data
 * 
 * Provides default Project Data for Codebender.cc
 */
class LoadProjectData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data into Project Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     */
    public function load(ObjectManager $manager)
    {
        $testProjectOne = new Project();
        $testProjectOne->setOwner($this->getReference('admin-user'));
        $testProjectOne->setName('test_project');
        $testProjectOne->setDescription('a project used to test the search function');
        $testProjectOne->setIsPublic(TRUE);
        $testProjectOne->setType('disk');
        $testProjectOne->setProjectfilesId('tester/152ebb4e77e233');
        $manager->persist($testProjectOne);

        $testProjectTwo = new Project();
        $testProjectTwo->setOwner($this->getReference('admin-user'));
        $testProjectTwo->setName('public_project');
        $testProjectTwo->setDescription('tests list');
        $testProjectTwo->setIsPublic(TRUE);
        $testProjectTwo->setType('disk');
        $testProjectTwo->setProjectfilesId('tester/152ebb508bc2bf');
        $manager->persist($testProjectTwo);

        $testProjectThree = new Project();
        $testProjectThree->setOwner($this->getReference('admin-user'));
        $testProjectThree->setName('private_project');
        $testProjectThree->setDescription('');
        $testProjectThree->setIsPublic(FALSE);
        $testProjectThree->setType('disk');
        $testProjectThree->setProjectfilesId('tester/152ebb53626ab8');
        $manager->persist($testProjectThree);

        $testProjectFour = new Project();
        $testProjectFour->setOwner($this->getReference('reg-user'));
        $testProjectFour->setName('sample');
        $testProjectFour->setDescription('');
        $testProjectFour->setIsPublic(TRUE);
        $testProjectFour->setType('disk');
        $testProjectFour->setProjectfilesId('testacc/152ebb54ce481c');
        $manager->persist($testProjectFour);

        $testProjectFive = new Project();
        $testProjectFive->setOwner($this->getReference('admin-user'));
        $testProjectFive->setName('createfileproj');
        $testProjectFive->setDescription('');
        $testProjectFive->setIsPublic(TRUE);
        $testProjectFive->setType('disk');
        $testProjectFive->setProjectfilesId('tester/152ebb5726a41e');
        $manager->persist($testProjectFive);


        $testProjectSix = new Project();
        $testProjectSix->setOwner($this->getReference('admin-user'));
        $testProjectSix->setName('delproj');
        $testProjectSix->setDescription('');
        $testProjectSix->setIsPublic(TRUE);
        $testProjectSix->setType('disk');
        $testProjectSix->setProjectfilesId('testacc/152ebb59615284');
        $manager->persist($testProjectSix);

        $testProjectSeven = new Project();
        $testProjectSeven->setOwner($this->getReference('reg-user'));
        $testProjectSeven->setName('renamefile');
        $testProjectSeven->setDescription('');
        $testProjectSeven->setIsPublic(TRUE);
        $testProjectSeven->setType('disk');
        $testProjectSeven->setProjectfilesId('testacc/152ebb5b4c2e3d');
        $manager->persist($testProjectSeven);
        // Commit all Projects to Database
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
