<?php

namespace Codebender\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Codebender\ProjectBundle\Entity\PrivateProjects;

/* Load Private Projects Data
 * 
 * Provides default Private Projects Data for Codebender.cc
 */
class LoadPrivateProjectsData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data into Private Projects Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     */
    public function load(ObjectManager $manager)
    {
        $testProjectOne = new PrivateProjects();
        $testProjectOne->setOwner($this->getReference('admin-user'));
        $testProjectOne->setDescription('test');
        $testProjectOne->setStarts(new \DateTime('2013-05-13'));
        $testProjectOne->setNumber(2);
        $manager->persist($testProjectOne);

        // Commit all Private Projects to Database
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
