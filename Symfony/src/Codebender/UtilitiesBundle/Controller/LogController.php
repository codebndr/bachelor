<?php

namespace Codebender\UtilitiesBundle\Controller;

use Codebender\UtilitiesBundle\Entity\Log;
use Codebender\UtilitiesBundle\Entity\ViewLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{
    /**
     * Logs User Action
     *
     * @param Integer $user
     * @param Integer $action
     * @param String $description
     * @param String $metadata
     * @return JSON encoded success
     */
    public function logAction($user, $action, $description, $metadata)
    {


        $log = new Log();
        $log->setUser($user);
        $log->setAction($action);
        $log->setDescription($description);
        $log->setMetadata($metadata);

        $log->setTimestamp(new \DateTime("now"));
        $this->em->persist($log);
        $this->em->flush();

        return new Response(json_encode(array('success' => true)));
    }

    /**
     * Logs when user views something
     *
     * @param Integer $user
     * @param Integer $action
     * @param String $description
     * @param String $metadata
     * @param String $session
     * @param Boolean $hasPreviousSession
     * @return JSON encoded success
     */
    public function logViewAction($user, $action, $description, $metadata, $session, $hasPreviousSession)
    {


        $log = new ViewLog();
        $log->setUser($user);
        $log->setAction($action);
        $log->setDescription($description);
        $log->setMetadata($metadata);
        $log->setTimestamp(new \DateTime("now"));
        $log->setSession($session);
        $log->setHasPreviousSession($hasPreviousSession);
        $this->em->persist($log);
        $this->em->flush();

        return new Response(json_encode(array('success' => true)));
    }

    /**
     * Constructor
     * 
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

}