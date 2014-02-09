<?php

namespace Codebender\UtilitiesBundle\Tests\Controller;


use Codebender\UtilitiesBundle\Controller\LogController;

class LogControllerUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testLogAction()
    {
        $em = $this->getMockBuilder("Doctrine\ORM\EntityManager")
            ->disableOriginalConstructor()
            ->setMethods(array("persist", "flush"))
            ->getMock();

        $controller = new LogController($em);

        $response = $controller->logAction(1,1,'ACTION','meta');

        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testLogViewAction()
    {
        $em = $this->getMockBuilder("Doctrine\ORM\EntityManager")
            ->disableOriginalConstructor()
            ->setMethods(array("persist", "flush"))
            ->getMock();

        $controller = new LogController($em);

        $response = $controller->logViewAction(1,1,'ACTION','meta','sessionId',true);

        $this->assertEquals($response->getContent(), '{"success":true}');
    }
} 