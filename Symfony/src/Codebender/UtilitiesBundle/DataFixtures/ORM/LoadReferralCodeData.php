<?php

namespace Codebender\UserBundle\DataFixtures\ORM;

use Codebender\UtilitiesBundle\Entity\ReferralCode;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


/* Load ReferralCode Data
 * 
 * Provides default ReferralCode Data for Codebender.cc
 */
class LoadReferralCodeData implements FixtureInterface
{

    /**
     * Load data into Users Table of Database
     *
     * This function is executed when the user runs the command: app/console doctrine:fixtures:load
     */
    public function load(ObjectManager $manager)
    {
        $code = new ReferralCode();
        $code->setName('test');
        $code->setDescription('just to test');
        $code->setCode('SecretSauce');
        $code->setPoints(40);
        $code->setAvailable(5);

        $manager->persist($code);
        $manager->flush();

    }

}
