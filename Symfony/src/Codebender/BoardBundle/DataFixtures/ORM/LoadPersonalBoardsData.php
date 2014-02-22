<?php

namespace Codebender\BoardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Codebender\BoardBundle\Entity\PersonalBoards;
use Doctrine\Common\DataFixtures\AbstractFixture;

/* Load Board Data
 * 
 * Provides default Board Data for CodeBender.cc
 */
class LoadPersonalBoardsData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data into Boards Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     */
    public function load(ObjectManager $manager)
    {

        $personal = new PersonalBoards();
        $personal->setOwner($this->getReference('admin-user'));
        $personal->setDescription("Tester\'s custom board");
        $personal->setStarts(new \DateTime('2013-05-13'));
        $personal->setExpires(null);
        $personal->setNumber(3);


        $manager->persist($personal);

        // Commit all Boards to Database
        $manager->flush();
    }
}
