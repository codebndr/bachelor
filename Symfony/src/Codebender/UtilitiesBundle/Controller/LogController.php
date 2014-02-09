<?php

namespace Codebender\UtilitiesBundle\Controller;

use Codebender\UtilitiesBundle\Entity\Log;
use Codebender\UtilitiesBundle\Entity\ViewLog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class LogController extends Controller
{

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

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

}