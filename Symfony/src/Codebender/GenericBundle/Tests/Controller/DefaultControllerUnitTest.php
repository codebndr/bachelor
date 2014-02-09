<?php

namespace Codebender\GenericBundle\Tests\Controller;
use Codebender\GenericBundle\Controller\DefaultController;
use Codebender\UserBundle\Controller\DefaultController as UserController;
use Codebender\ProjectBundle\Controller\SketchController;
use Codebender\UtilitiesBundle\Handler\DefaultHandler as UtilitiesHandler;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexAction_NotLoggedIn()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('isGranted'))
            ->getMockForAbstractClass();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('security.context'))->will($this->returnValue($security));
        $security->expects($this->once())->method('isGranted')->with($this->equalTo('ROLE_USER'))->will($this->returnValue(false));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(null),$this->equalTo(1),$this->equalTo('HOME_PAGE_VIEW'),$this->equalTo(""),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Index:index.html.twig'))->will($this->returnValue('index'));

        $res = $controller->indexAction();

        $this->assertEquals($res, 'index');
    }

    public function testIndexAction_LoggedIn_HasNoPrivateProject()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('isGranted'))
            ->getMockForAbstractClass();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "getTopUsersAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("canCreatePrivateProjectAction"))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('security.context'))->will($this->returnValue($security));
        $security->expects($this->once())->method('isGranted')->with($this->equalTo('ROLE_USER'))->will($this->returnValue(true));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->at(1))->method('getTopUsersAction')->with($this->equalTo(5))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('canCreatePrivateProjectAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));



        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(1),$this->equalTo(1),$this->equalTo('HOME_PAGE_VIEW'),$this->equalTo(""),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Index:list.html.twig'), $this->equalTo(array('user' => array('success' => true, 'id' => 1), 'popular_users'=>array(), 'avail_priv_proj' => array('success' => false, 'available' => 0))))->will($this->returnValue('list'));

        $res = $controller->indexAction();

        $this->assertEquals($res, 'list');
    }

    public function testIndexAction_LoggedIn_HasTwoPrivateProjects()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('isGranted'))
            ->getMockForAbstractClass();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "getTopUsersAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("canCreatePrivateProjectAction"))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('security.context'))->will($this->returnValue($security));
        $security->expects($this->once())->method('isGranted')->with($this->equalTo('ROLE_USER'))->will($this->returnValue(true));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->at(1))->method('getTopUsersAction')->with($this->equalTo(5))->will($this->returnValue(new Response('{"success":true, "list":["user1","user2","user3","user4","user5"]}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('canCreatePrivateProjectAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "available":2}')));



        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->once())->method('logViewAction')->with($this->equalTo(1),$this->equalTo(1),$this->equalTo('HOME_PAGE_VIEW'),$this->equalTo(""),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Index:list.html.twig'), $this->equalTo(array('user' => array('success' => true, 'id' => 1), 'popular_users'=>array("user1","user2","user3","user4","user5"), 'avail_priv_proj' => array('success' => true, 'available' => 2))))->will($this->returnValue('list'));

        $res = $controller->indexAction();

        $this->assertEquals($res, 'list');
    }

    public function testUserAction_Success()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getUserAction"))
            ->getMock();
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo("nonexistent_user"))->will($this->returnValue(new Response('{"success":true, "id":1, "twitter":"codebender_cc", "email":"girder@codebender.cc"}')));

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction"))
            ->getMock();
        $projectmanager->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response("[]")));

        /** @var UtilitiesHandler $utilities_handler */
        $utilities_handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get_gravatar", "get"))
            ->getMock();
        $utilities_handler->expects($this->once())->method('get')->with($this->equalTo("http://api.twitter.com/1/statuses/user_timeline/codebender_cc.json"))->will($this->returnValue('[{"text":"a tweet"}]'));
        $utilities_handler->expects($this->once())->method('get_gravatar')->with($this->equalTo("girder@codebender.cc"))->will($this->returnValue("fake_gravatar"));

        /** @var DefaultController $controller */
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));
        $controller->expects($this->at(0))->method('get')->with($this->equalTo("codebender_user.usercontroller"))->will($this->returnValue($usercontroller));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo("codebender_project.sketchmanager"))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo("codebender_utilities.handler"))->will($this->returnValue($utilities_handler));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:user.html.twig"), $this->equalTo(array('user' => array('success' => true, 'id' => 1, 'twitter' => 'codebender_cc', 'email' => 'girder@codebender.cc'), 'projects' => array(), 'image' => 'fake_gravatar', 'lastTweet' => 'a tweet')))->will($this->returnValue(new Response("minor_error_response")));

        $response = $controller->userAction("nonexistent_user");
        $this->assertEquals("minor_error_response", $response->getContent());
    }

    public function testUserAction_JsonSuccess()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getUserAction"))
            ->getMock();
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo("json_user"))->will($this->returnValue(new Response('{"success":true, "id":1, "username":"json_user", "firstname":"John", "lastname":"Doe", "twitter":"codebender_cc", "email":"girder@codebender.cc"}')));

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction"))
            ->getMock();
        $projectmanager->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response(json_encode(array(array("id"=> 1, "name"=>"name", "description"=>'description', "is_public"=>true))))));
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get","generateUrl"));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo("codebender_user.usercontroller"))->will($this->returnValue($usercontroller));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo("codebender_project.sketchmanager"))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(2))->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_project"), $this->equalTo(array('id' => 1)), $this->equalTo(true))->will($this->returnValue("project_url.codebender.cc"));
        $controller->expects($this->at(3))->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_json_project"), $this->equalTo(array('id' => 1)), $this->equalTo(true))->will($this->returnValue("json_project_url.codebender.cc"));
        $response = $controller->userAction("json_user.json");

        $this->assertEquals('{"success":true,"username":"json_user","name":"John Doe","projects":[{"id":1,"name":"name","description":"description","url":"project_url.codebender.cc","json_url":"json_project_url.codebender.cc"}]}', $response->getContent());
    }

    public function testUserAction_JsonNoUser()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getUserAction"))
            ->getMock();
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo("json_user"))->will($this->returnValue(new Response('{"success":false}')));
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get","generateUrl"));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo("codebender_user.usercontroller"))->will($this->returnValue($usercontroller));
        $response = $controller->userAction("json_user.json");
        $this->assertEquals('{"success":false,"error":"There is no such user!"}', $response->getContent());
    }


    public function testUserAction_TwitterError()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getUserAction"))
            ->getMock();
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo("nonexistent_user"))->will($this->returnValue(new Response('{"success":true, "id":1, "twitter":"codebender_cc", "email":"girder@codebender.cc"}')));

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction"))
            ->getMock();
        $projectmanager->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response("[]")));

        /** @var UtilitiesHandler $utilities_handler */
        $utilities_handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get_gravatar", "get"))
            ->getMock();
        $utilities_handler->expects($this->once())->method('get')->with($this->equalTo("http://api.twitter.com/1/statuses/user_timeline/codebender_cc.json"))->will($this->returnValue('{"errors":[{"message":"Sorry, that page does not exist","code":34}]}'));
        $utilities_handler->expects($this->once())->method('get_gravatar')->with($this->equalTo("girder@codebender.cc"))->will($this->returnValue("fake_gravatar"));

        /** @var DefaultController $controller */
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));
        $controller->expects($this->at(0))->method('get')->with($this->equalTo("codebender_user.usercontroller"))->will($this->returnValue($usercontroller));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo("codebender_project.sketchmanager"))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo("codebender_utilities.handler"))->will($this->returnValue($utilities_handler));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:user.html.twig"), $this->equalTo(array('user' => array('success' => true, 'id' => 1, 'twitter' => 'codebender_cc', 'email' => 'girder@codebender.cc'), 'projects' => array(), 'image' => 'fake_gravatar', 'lastTweet' => false)))->will($this->returnValue(new Response("minor_error_response")));

        $response = $controller->userAction("nonexistent_user");
        $this->assertEquals("minor_error_response", $response->getContent());
    }

    public function testUserAction_NoUser()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getUserAction"))
            ->getMock();
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo("nonexistent_user"))->will($this->returnValue(new Response('{"success":false}')));

        /** @var DefaultController $controller */
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));
        $controller->expects($this->once())->method('get')->with($this->equalTo("codebender_user.usercontroller"))->will($this->returnValue($usercontroller));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:minor_error.html.twig"), $this->equalTo(array('error' => "There is no such user.")))->will($this->returnValue(new Response("minor_error_response")));

        $response = $controller->userAction("nonexistent_user");
        $this->assertEquals("minor_error_response", $response->getContent());
    }

    public function testProjectAction_SuccessEditor()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "forward", "getRequest"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(3))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(12),$this->equalTo('EDITOR_PROJECT_VIEW'),$this->equalTo(json_encode(array("project" => 1))),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->at(4))->method('forward')->with($this->equalTo('CodebenderGenericBundle:Editor:edit'), $this->equalTo(array("id" => 1)))->will($this->returnValue('forward_response'));

        $res = $controller->projectAction(1);

        $this->assertEquals($res, "forward_response");

    }

    public function testProjectAction_SuccessEmbedded()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction', 'checkReadProjectPermissionsAction', 'getOwnerAction', 'getNameAction', 'getParentAction', 'listFilesAction'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $projectmanager->expects($this->at(2))->method('checkReadProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(3))->method('getOwnerAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response": {"id":1, "username":"awesomeuser", "firstname":"project", "lastname":"owner"}}')));
        $projectmanager->expects($this->at(4))->method('getNameAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response":"project"}')));
        $projectmanager->expects($this->at(5))->method('getParentAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": false}')));

        $projectmanager->expects($this->at(6))->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));

        $router->expects($this->at(0))->method('generate')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project_url'));
        $router->expects($this->at(1))->method('generate')->with($this->equalTo('CodebenderGenericBundle_user'), $this->equalTo(array('user' => 'awesomeuser')))->will($this->returnValue('user_url'));
        $router->expects($this->at(2))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_clone'), $this->equalTo(array('id' => 1)))->will($this->returnValue('clone_url'));
        $router->expects($this->at(3))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_download'), $this->equalTo(array('id' => 1)))->will($this->returnValue('download_url'));


        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(4))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(13),$this->equalTo('EMBEDDED_PROJECT_VIEW'),$this->equalTo(json_encode(array("project" => 1))),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:project_embeddable.html.twig'), $this->equalTo(array('json'=>'{"project":{"name":"project","url":"project_url"},"user":{"name":"awesomeuser","url":"user_url"},"clone_url":"clone_url","download_url":"download_url","files":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')))->will($this->returnValue('embedded_project_view'));
        $res = $controller->projectAction(1, true, false);

        $this->assertEquals($res, 'embedded_project_view');

    }

    public function testProjectAction_SuccessJson()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction', 'checkReadProjectPermissionsAction', 'getOwnerAction', 'getNameAction', 'getParentAction', 'listFilesAction'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $projectmanager->expects($this->at(2))->method('checkReadProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(3))->method('getOwnerAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response": {"id":1, "username":"awesomeuser", "firstname":"project", "lastname":"owner"}}')));
        $projectmanager->expects($this->at(4))->method('getNameAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response":"project"}')));
        $projectmanager->expects($this->at(5))->method('getParentAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response": {"id":2, "username":"parentAwesomeuser", "firstname":"ParentProject", "lastname":"owner"}}')));

        $projectmanager->expects($this->at(6))->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));

        $router->expects($this->at(0))->method('generate')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project_url'));
        $router->expects($this->at(1))->method('generate')->with($this->equalTo('CodebenderGenericBundle_user'), $this->equalTo(array('user' => 'awesomeuser')))->will($this->returnValue('user_url'));
        $router->expects($this->at(2))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_clone'), $this->equalTo(array('id' => 1)))->will($this->returnValue('clone_url'));
        $router->expects($this->at(3))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_download'), $this->equalTo(array('id' => 1)))->will($this->returnValue('download_url'));


        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(4))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(14),$this->equalTo('JSON_PROJECT_VIEW'),$this->equalTo(json_encode(array("project" => 1))),$this->equalTo("sessionId"), $this->equalTo(true));

        $res = $controller->projectAction(1, false, true);

        $this->assertEquals($res->getContent(), '{"project":{"name":"project","url":"project_url"},"user":{"name":"awesomeuser","url":"user_url"},"clone_url":"clone_url","download_url":"download_url","files":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}],"success":true}');
        $this->assertEquals($res->headers->get('content_type'), "text/json");
    }

    public function testProjectAction_SuccessProjectView()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction', 'checkReadProjectPermissionsAction', 'getOwnerAction', 'getNameAction', 'getParentAction', 'listFilesAction'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $projectmanager->expects($this->at(2))->method('checkReadProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(3))->method('getOwnerAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response": {"id":1, "username":"awesomeuser", "firstname":"project", "lastname":"owner"}}')));
        $projectmanager->expects($this->at(4))->method('getNameAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response":"project"}')));
        $projectmanager->expects($this->at(5))->method('getParentAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success": true, "response": {"id":2, "username":"parentAwesomeuser", "firstname":"ParentProject", "lastname":"owner"}}')));

        $projectmanager->expects($this->at(6))->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));

        $router->expects($this->at(0))->method('generate')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project_url'));
        $router->expects($this->at(1))->method('generate')->with($this->equalTo('CodebenderGenericBundle_user'), $this->equalTo(array('user' => 'awesomeuser')))->will($this->returnValue('user_url'));
        $router->expects($this->at(2))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_clone'), $this->equalTo(array('id' => 1)))->will($this->returnValue('clone_url'));
        $router->expects($this->at(3))->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_download'), $this->equalTo(array('id' => 1)))->will($this->returnValue('download_url'));


        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(4))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(15),$this->equalTo('PROJECT_VIEW'),$this->equalTo(json_encode(array("project" => 1))),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:project.html.twig'), $this->equalTo(array('project_name' => 'project', 'owner' => array('id' => 1, 'username' => 'awesomeuser', 'firstname'=> 'project', 'lastname' => 'owner'), 'files' => array(array('filename' => 'header.h', 'code'=>''), array('filename' => 'project.ino', 'code'=>'')), 'project_id'=>1, 'parent'=>array('id' => 2, 'username' => 'parentAwesomeuser', 'firstname'=> 'ParentProject', 'lastname' => 'owner'), 'json'=>'{"project":{"name":"project","url":"project_url"},"user":{"name":"awesomeuser","url":"user_url"},"clone_url":"clone_url","download_url":"download_url","files":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')))->will($this->returnValue('project_view'));
        $res = $controller->projectAction(1);

        $this->assertEquals($res, 'project_view');


    }

    public function testProjectAction_NoProjectNotJson()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $reflection = new \ReflectionClass($controller);
        $reflection_property = $reflection->getProperty('container');
        $reflection_property->setAccessible(true);

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:minor_error.html.twig'), $this->equalTo(array('error' => "There is no such project!")))->will($this->returnValue('render_minor_error'));

        $res = $controller->projectAction(1);

        $this->assertEquals($res, "render_minor_error");
    }
    public function testProjectAction_NoProjectJson()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $reflection = new \ReflectionClass($controller);
        $reflection_property = $reflection->getProperty('container');
        $reflection_property->setAccessible(true);

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $res = $controller->projectAction(1, false, true);

        $this->assertEquals($res->getContent(), '{"success":false,"error":"There is no such project!"}');
    }

    public function testProjectAction_NoReadPermissionsNotJson()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction', 'checkReadProjectPermissionsAction'))
            ->getMock();


        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $reflection = new \ReflectionClass($controller);
        $reflection_property = $reflection->getProperty('container');
        $reflection_property->setAccessible(true);

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $projectmanager->expects($this->at(2))->method('checkReadProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:minor_error.html.twig'), $this->equalTo(array('error' => "There is no such project!")))->will($this->returnValue('render_minor_error'));
        $res = $controller->projectAction(1);

        $this->assertEquals($res, 'render_minor_error');


    }

    public function testProjectAction_NoReadPermissionsJson()
    {
        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        /** @var SketchController $projectmanager */
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'checkWriteProjectPermissionsAction', 'checkReadProjectPermissionsAction'))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $reflection = new \ReflectionClass($controller);
        $reflection_property = $reflection->getProperty('container');
        $reflection_property->setAccessible(true);

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $container->expects($this->at(0))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(1))->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->at(2))->method('checkReadProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));
        $res = $controller->projectAction(1, false, true);

        $this->assertEquals($res->getContent(), '{"success":false,"error":"There is no such project!"}');
    }

    /**
     * @runInSeparateProcess
     */
    public function testProjectfilesAction_Success()
    {
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'listFilesAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $request->request = $req;

        $req->expects($this->once())->method('get')->with($this->equalTo('project_id'))->will($this->returnValue(1));

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $projectmanager->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));

        $res = $controller->projectfilesAction();
        $this->assertEquals($res->getContent(), '{"header.h":"","project.ino":""}');
    }

    /**
     * @runInSeparateProcess
     */
    public function testProjectfilesAction_NoProject()
    {
        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("checkExistsAction", 'listFilesAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "getRequest"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $request->request = $req;

        $req->expects($this->once())->method('get')->with($this->equalTo('project_id'))->will($this->returnValue(1));

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('checkExistsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $res = $controller->projectfilesAction();
        $this->assertEquals($res->getContent(), 'Project Not Found');
    }


    public function testLibrariesAction_success()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render", "getRequest"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url'))->will($this->returnValue('{"success":true,"text":"Successful Request!","categories":{"Examples":[],"Builtin Libraries":[],"External Libraries":[]}}'));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));

        $container->expects($this->once())->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(3))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(2),$this->equalTo('LIBRARY_PAGE_VIEW'),$this->equalTo(""),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:libraries.html.twig"), $this->equalTo(array('categories' => array('Examples' => array(), 'Builtin Libraries' => array(),'External Libraries' => array()))))->will($this->returnValue('libraries_view'));
        $response = $controller->librariesAction();

        $this->assertEquals($response, "libraries_view");

    }

    public function testLibrariesAction_LibraryReqFalse()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url'))->will($this->returnValue('{"success":false}'));


        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:minor_error.html.twig"), $this->equalTo(array('error' => "Sorry! The library list could not be fetched.")))->will($this->returnValue('minor_error'));
        $response = $controller->librariesAction();

        $this->assertEquals($response, "minor_error");

    }

    public function testLibrariesAction_LibraryReqNotJson()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url'))->will($this->returnValue('Not_JSON'));


        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:minor_error.html.twig"), $this->equalTo(array('error' => "Sorry! The library list could not be fetched.")))->will($this->returnValue('minor_error'));
        $response = $controller->librariesAction();

        $this->assertEquals($response, "minor_error");

    }


    public function testExampleAction_successEmbed()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render", "getRequest"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url/get/lib/example'))->will($this->returnValue('{"success":true,"files":[{"filename":"example.ino","code":""}]}'));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));

        $container->expects($this->once())->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));

        $router->expects($this->once())->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_downloadexample'), $this->equalTo(array('name' => 'example', 'url' => "/get/lib/example" )), $this->equalTo(true))->will($this->returnValue('downloadUrl'));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(4))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(16),$this->equalTo('EMBEDDED_LIBRARY_EXAMPLE_VIEW'),$this->equalTo('{"library":"lib","example":"example"}'),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:project_embeddable.html.twig"), $this->equalTo(array('library' => 'lib', 'example' => 'example', 'files' => array(array('filename' => 'example.ino', 'code' => '')), 'type' => 'example', 'json' => '{"project":{"name":"example","url":""},"user":{"name":"","url":""},"clone_url":"","download_url":"downloadUrl","files":[{"filename":"example.ino","code":""}]}')))->will($this->returnValue('embed_example_view'));
        $response = $controller->exampleAction('lib', 'example', true);

        $this->assertEquals($response, "embed_example_view");

    }

    public function testExampleAction_success()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render", "getRequest"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        /** @var UserController $usercontroller */
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url/get/lib/example'))->will($this->returnValue('{"success":true,"files":[{"filename":"example.ino","code":""}]}'));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));

        $container->expects($this->once())->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));

        $router->expects($this->once())->method('generate')->with($this->equalTo('CodebenderUtilitiesBundle_downloadexample'), $this->equalTo(array('name' => 'example', 'url' => "/get/lib/example" )), $this->equalTo(true))->will($this->returnValue('downloadUrl'));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(4))->method('getRequest')->will($this->returnValue($request));

        $request->expects($this->at(0))->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(4),$this->equalTo('LIBRARY_EXAMPLE_VIEW'),$this->equalTo('{"library":"lib","example":"example"}'),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:example.html.twig"), $this->equalTo(array('library' => 'lib', 'example' => 'example', 'files' => array(array('filename' => 'example.ino', 'code' => '')), 'type' => 'example', 'json' => '{"project":{"name":"example","url":""},"user":{"name":"","url":""},"clone_url":"","download_url":"downloadUrl","files":[{"filename":"example.ino","code":""}]}')))->will($this->returnValue('example_view'));
        $response = $controller->exampleAction('lib', 'example');

        $this->assertEquals($response, "example_view");

    }

    public function testExampleAction_LibraryReqFalse()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url/get/lib/example'))->will($this->returnValue('{"success":false}'));


        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:minor_error.html.twig"), $this->equalTo(array('error' => "Sorry! The library list could not be fetched.")))->will($this->returnValue('minor_error'));
        $response = $controller->exampleAction('lib', 'example');

        $this->assertEquals($response, "minor_error");

    }

    public function testExampleAction_LibraryReqNotJson()
    {
        $utilitiesHandler = $this->getMockBuilder('Codebender\Utilities\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library_url'));
        $utilitiesHandler->expects($this->once())->method('get')->with($this->equalTo('library_url/get/lib/example'))->will($this->returnValue('Not_JSON'));


        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderGenericBundle:Default:minor_error.html.twig"), $this->equalTo(array('error' => "Sorry! The library list could not be fetched.")))->will($this->returnValue('minor_error'));
        $response = $controller->exampleAction('lib', 'example');

        $this->assertEquals($response, "minor_error");

    }

    /**
     * @runInSeparateProcess
     */
    public function testBoardsAction_notLoggedIn()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render", "getRequest"));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $boardcontroller = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction"))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardcontroller));
        $boardcontroller->expects($this->once())->method('listAction')->will($this->returnValue(new Response('[{"name":"Arduino Uno","upload":{},"bootloader":{},"build":{},"description":"","personal":false,"id":1},{"name":"Arno","upload":{},"bootloader":{},"build":{},"description":"","personal":false,"id":2}]')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $container->expects($this->once())->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->once())->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(null),$this->equalTo(3),$this->equalTo('BOARDS_PAGE_VIEW'),$this->equalTo(''),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:boards.html.twig'), $this->equalTo(array('boards' => array(array('name' => 'Arduino Uno','upload' => array(),'bootloader' => array(),'build' => array(),'description' => '','personal' => false,'id' => 1), array('name' => 'Arno','upload' => array(),'bootloader' => array(),'build' => array(),'description' => '','personal' => false,'id' => 2)), 'available_boards' => array('success' => false, 'available' => 0))))->will($this->returnValue('boards'));
        $res = $controller->boardsAction();

        $this->assertEquals($res, "boards");

    }

    public function testBoardsAction_LoggedIn()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("get", "render", "getRequest"));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logViewAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $boardcontroller = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction", "canAddPersonalBoardAction"))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter', 'get'))
            ->getMockForAbstractClass();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->setMethods(array('hasPreviousSession'))
            ->getMock();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardcontroller));
        $boardcontroller->expects($this->once())->method('listAction')->will($this->returnValue(new Response('[{"name":"Arduino Uno","upload":{},"bootloader":{},"build":{},"description":"","personal":false,"id":1},{"name":"Arno","upload":{},"bootloader":{},"build":{},"description":"","personal":false,"id":2},{"name":"Personal","upload":{},"bootloader":{},"build":{},"description":"","personal":true,"id":3}]')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $boardcontroller->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo('1'))->will($this->returnValue(new Response(json_encode(array('success' => true, 'available_boards' => 1)))));
        $container->expects($this->once())->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->at(0))->method('getId')->will($this->returnValue("sessionId"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $request->expects($this->once())->method('hasPreviousSession')->will($this->returnValue(true));
        $logController->expects($this->at(0))->method('logViewAction')->with($this->equalTo(1),$this->equalTo(3),$this->equalTo('BOARDS_PAGE_VIEW'),$this->equalTo(''),$this->equalTo("sessionId"), $this->equalTo(true));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Default:boards.html.twig'), $this->equalTo(array('boards' => array(array('name' => 'Arduino Uno','upload' => array(),'bootloader' => array(),'build' => array(),'description' => '','personal' => false,'id' => 1), array('name' => 'Arno','upload' => array(),'bootloader' => array(),'build' => array(),'description' => '','personal' => false,'id' => 2), array('name' => 'Personal','upload' => array(),'bootloader' => array(),'build' => array(),'description' => '','personal' => true,'id' => 3)), 'available_boards' => array('success' => true, 'available_boards' => 1))))->will($this->returnValue('boards'));
        $res = $controller->boardsAction();

        $this->assertEquals($res, "boards");

    }

    public function testEmbeddedCompilerFlasherAction()
    {
        $controller = $this->getMock("Codebender\GenericBundle\Controller\DefaultController", array("render"));
        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:CompilerFlasher:compilerflasher.js.twig'))->will($this->returnValue(new Response('compilerflasher')));

        $response = $controller->embeddedCompilerFlasherJavascriptAction();
        $this->assertEquals($response->getContent(), "compilerflasher");
        $this->assertEquals($response->headers->get('content_type'), "text/javascript");
    }
}
