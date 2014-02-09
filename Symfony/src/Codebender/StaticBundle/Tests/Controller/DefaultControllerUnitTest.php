<?php

namespace Codebender\StaticBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testAboutAction_LoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(1),$this->equalTo(5),$this->equalTo("ABOUT_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:about.html.twig"))->will($this->returnValue('about_page'));

        $res = $controller->aboutAction();

        $this->assertEquals($res, 'about_page');
    }

    public function testAboutAction_notLoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(5),$this->equalTo("ABOUT_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:about.html.twig"))->will($this->returnValue('about_page'));

        $res = $controller->aboutAction();

        $this->assertEquals($res, 'about_page');
    }

    public function testTechAction_LoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(1),$this->equalTo(6),$this->equalTo("TECH_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:tech.html.twig"))->will($this->returnValue('tech_page'));

        $res = $controller->techAction();

        $this->assertEquals($res, 'tech_page');
    }

    public function testTechAction_notLoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(6),$this->equalTo("TECH_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:tech.html.twig"))->will($this->returnValue('tech_page'));

        $res = $controller->techaction();

        $this->assertEquals($res, 'tech_page');
    }



    public function testTutorialsAction_LoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(1),$this->equalTo(8),$this->equalTo("TUTORIALS_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:tutorials.html.twig"))->will($this->returnValue('tutorials_page'));

        $res = $controller->tutorialsAction();

        $this->assertEquals($res, 'tutorials_page');
    }

    public function testTutorialsAction_notLoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(8),$this->equalTo("TUTORIALS_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:tutorials.html.twig"))->will($this->returnValue('tutorials_page'));

        $res = $controller->tutorialsAction();

        $this->assertEquals($res, 'tutorials_page');
    }

    public function testTeamAction()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(7),$this->equalTo("TEAM_PAGE_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:team.html.twig"))->will($this->returnValue('team_page'));

        $res = $controller->teamAction();

        $this->assertEquals($res, 'team_page');
    }

    public function testPluginAction()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("render"));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:plugin.html.twig"), $this->equalTo(array()))->will($this->returnValue('plugin_page'));
        $res=$controller->pluginAction();
        $this->assertEquals($res,'plugin_page');
    }

    public function testPartnerAction_Exists()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("render"));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Partner:arno.html.twig"), $this->equalTo(array()))->will($this->returnValue('arno_page'));
        $res=$controller->partnerAction('arno');
        $this->assertEquals($res,'arno_page');
    }

    public function testPartnerAction_DoesNotExist()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("generateUrl", "redirect"));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('codebender.cc'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('codebender.cc'))->will($this->returnValue('fake-redirect-response'));
        $res=$controller->partnerAction('inexistent');
        $this->assertEquals($res,'fake-redirect-response');
    }

    public function testInfoPointsAction()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("render"));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:info_points.html.twig"), $this->equalTo(array()))->will($this->returnValue('info_points'));
        $res=$controller->infoPointsAction();
        $this->assertEquals($res,'info_points');
    }

    public function testInfoKarmaAction()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("render"));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:info_karma.html.twig"), $this->equalTo(array()))->will($this->returnValue('info_karma'));
        $res=$controller->infoKarmaAction();
        $this->assertEquals($res,'info_karma');
    }

    public function testInfoPrivateProjectsAction_LoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("currentPrivateProjectRecordsAction"))
            ->getMock();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(9),$this->equalTo("PRIVATE_PROJECTS_INFO_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('currentPrivateProjectRecordsAction')->will($this->returnValue(new Response('{"success":true, "message":"User records retrieved successfully","list":""}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:info_private_projects.html.twig"), $this->equalTo(array("records" => array("success" => true, "message" => "User records retrieved successfully", "list" => ""))))->will($this->returnValue('private_projects_info'));

        $res = $controller->infoPrivateProjectsAction();

        $this->assertEquals($res, 'private_projects_info');
    }

    public function testInfoPrivateProjectsAction_NotLoggedIn()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("currentPrivateProjectRecordsAction"))
            ->getMock();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(9),$this->equalTo("PRIVATE_PROJECTS_INFO_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('currentPrivateProjectRecordsAction')->will($this->returnValue(new Response('{"success":false, "message":"User not logged in."}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:info_private_projects.html.twig"), $this->equalTo(array("records" => array("success" => false, "message" => "User not logged in."))))->will($this->returnValue('private_projects_info'));

        $res = $controller->infoPrivateProjectsAction();

        $this->assertEquals($res, 'private_projects_info');
    }

    public function testUploadBootloaderAction()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(10),$this->equalTo("UPLOAD_BOOTLOADER_VIEW"),$this->equalTo(""),$this->equalTo("sessionId"),$this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Default:upload_bootloader.html.twig"))->will($this->returnValue('upload_bootloader_page'));

        $res = $controller->uploadBootloaderAction();

        $this->assertEquals($res, 'upload_bootloader_page');
    }

    public function testWalkthroughAction_PageExists()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","setWalkthroughStatusAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(11),$this->equalTo("WALKTHROUGH_VIEW"),$this->equalTo('{"page":"1"}'),$this->equalTo("sessionId"),$this->equalTo(true));

        $usercontroller->expects($this->once())->method('setWalkthroughStatusAction')->with($this->equalTo('1'));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderStaticBundle:Walkthrough:page1.html.twig"), $this->equalTo(array("page"=>'1')))->will($this->returnValue('walktrough_page_1'));

        $res = $controller->walkthroughAction('1');

        $this->assertEquals($res, 'walktrough_page_1');
    }

    public function testWalkthroughAction_DownloadComplete()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("render"));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderStaticBundle:Walkthrough:download-complete.html.twig'))->will($this->returnValue('download-complete-response'));

        $res = $controller->walkthroughAction('download-complete');

        $this->assertEquals($res, 'download-complete-response');
    }

    public function testWalkthroughAction_Invalid()
    {
        $controller = $this->getMock("Codebender\StaticBundle\Controller\DefaultController", array("redirect", "generateUrl"));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('codebender.cc'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('codebender.cc'))->will($this->returnValue('fake-redirect-response'));

        $res = $controller->walkthroughAction('invalid');

        $this->assertEquals($res, 'fake-redirect-response');
    }

}
