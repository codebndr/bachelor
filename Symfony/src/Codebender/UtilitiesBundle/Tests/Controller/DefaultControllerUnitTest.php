<?php

namespace Codebender\UtilitiesBundle\Tests\Controller;
use Codebender\UtilitiesBundle\Controller\DefaultController;
use Codebender\UtilitiesBundle\Handler\DefaultHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testNewprojectAction_projectSuccess()
{
    $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
        ->disableOriginalConstructor()
        ->setMethods(array("getCurrentUserAction"))
        ->getMock();

    $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

    $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
        ->disableOriginalConstructor()
        ->setMethods(array('logAction'))
        ->getMock();

    $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
        ->disableOriginalConstructor()
        ->setMethods(array("createprojectAction"))
        ->getMock();

    $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
        ->disableOriginalConstructor()
        ->getMock();

    $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
        ->disableOriginalConstructor()
        ->setMethods(array("default_text"))
        ->getMock();

    $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

    $request->request = $req;

    $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


    $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
    $controller->expects($this->at(1))->method('getRequest')->will($this->returnValue($request));

    $req->expects($this->at(0))->method('get')->with($this->equalTo('project_name'))->will($this->returnValue('name'));

    $req->expects($this->at(1))->method('get')->with($this->equalTo('isPublic'))->will($this->returnValue(null));
    $req->expects($this->at(2))->method('get')->with($this->equalTo('code'))->will($this->returnValue(null));
    $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));

    $handler->expects($this->once())->method('default_text')->will($this->returnValue(''));

    $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

    $projectmanager->expects($this->once())->method('createprojectAction')->with($this->equalTo(1), $this->equalTo('name'), '', true)->will($this->returnValue(new Response('{"success":true,"id":1}')));

    $controller->expects($this->at(4))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

    $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(0), $this->equalTo('CREATE_PROJECT'), $this->equalTo('{"success":true,"project":1,"is_public":true}'));

    $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project url'));
    $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake project redirect'));
    $response = $controller->newprojectAction();

    $this->assertEquals($response, 'fake project redirect');


}

    public function testNewprojectAction_notLoggedIn()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));


        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('project url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake homepage redirect'));
        $response = $controller->newprojectAction();

        $this->assertEquals($response, 'fake homepage redirect');


    }

    public function testNewprojectAction_projectfail()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createprojectAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("default_text"))
            ->getMock();


        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();



        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->at(0))->method('get')->with($this->equalTo('project_name'))->will($this->returnValue('name'));

        $req->expects($this->at(1))->method('get')->with($this->equalTo('isPublic'))->will($this->returnValue('false'));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('code'))->will($this->returnValue(null));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));

        $handler->expects($this->once())->method('default_text')->will($this->returnValue(''));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createprojectAction')->with($this->equalTo(1), $this->equalTo('name'), '', false)->will($this->returnValue(new Response('{"success":false,"error":"Cannot create private project."}')));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $controller->expects($this->at(5))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo('error'), $this->equalTo('Error: Cannot create private project.'));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(0), $this->equalTo('CREATE_PROJECT'), $this->equalTo('{"success":false,"is_public":false,"error":"Cannot create private project."}'));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('project url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake homepage redirect'));
        $response = $controller->newprojectAction();

        $this->assertEquals($response, 'fake homepage redirect');


    }

    public function testNewprojectAction_libExamplewithoutHeaderSuccess()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createprojectAction", "createFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();


        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->at(0))->method('get')->with($this->equalTo('project_name'))->will($this->returnValue('project'));

        $req->expects($this->at(1))->method('get')->with($this->equalTo('isPublic'))->will($this->returnValue(null));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('code'))->will($this->returnValue(json_encode(array(array('filename' => 'project.ino', 'code' => '')))));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createprojectAction')->with($this->equalTo(1), $this->equalTo('project'), '', true)->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(9), $this->equalTo('CLONE_LIB_EXAMPLE'), $this->equalTo('{"success":true,"project":1,"is_public":true}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake project redirect'));
        $response = $controller->newprojectAction();

        $this->assertEquals($response, 'fake project redirect');


    }

    public function testNewprojectAction_projectPrivateSuccess()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createprojectAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("default_text"))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->at(0))->method('get')->with($this->equalTo('project_name'))->will($this->returnValue('name'));

        $req->expects($this->at(1))->method('get')->with($this->equalTo('isPublic'))->will($this->returnValue('false'));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('code'))->will($this->returnValue(null));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));

        $handler->expects($this->once())->method('default_text')->will($this->returnValue(''));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createprojectAction')->with($this->equalTo(1), $this->equalTo('name'), '', false)->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(4))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(0), $this->equalTo('CREATE_PROJECT'), $this->equalTo('{"success":true,"project":1,"is_public":false}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake project redirect'));
        $response = $controller->newprojectAction();

        $this->assertEquals($response, 'fake project redirect');


    }


    public function testNewprojectAction_libExamplewithHeaderSuccess()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createprojectAction", "createFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();


        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));


        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->at(0))->method('get')->with($this->equalTo('project_name'))->will($this->returnValue('project'));

        $req->expects($this->at(1))->method('get')->with($this->equalTo('isPublic'))->will($this->returnValue(null));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('code'))->will($this->returnValue(json_encode(array(array('filename' => 'project.ino', 'code' => ''), array('filename' => 'header.h', 'code' => '')))));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createprojectAction')->with($this->equalTo(1), $this->equalTo('project'), '', true)->will($this->returnValue(new Response('{"success":true,"id":1}')));
        $projectmanager->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo('header.h'), '')->will($this->returnValue(new Response('{"success":true,"message":File created successfully.}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(9), $this->equalTo('CLONE_LIB_EXAMPLE'), $this->equalTo('{"success":true,"project":1,"is_public":true}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 1)))->will($this->returnValue('project url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('project url'))->will($this->returnValue('fake project redirect'));
        $response = $controller->newprojectAction();

        $this->assertEquals($response, 'fake project redirect');


    }

	public function testDeleteprojectAction_success()
	{
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('deleteAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(3), $this->equalTo('DELETE_PROJECT'), $this->equalTo('{"success":true,"project":1}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('codebender.cc'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('codebender.cc'))->will($this->returnValue('codebender.cc_fake_redirect'));

        $response = $controller->deleteprojectAction(1);
        $this->assertEquals($response,'codebender.cc_fake_redirect' );
    }

    public function testDeleteprojectAction_failure()
    {
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "generateUrl", "redirect"));

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('deleteAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(3), $this->equalTo('DELETE_PROJECT'), $this->equalTo('{"success":false,"project":1}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('codebender.cc'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('codebender.cc'))->will($this->returnValue('codebender.cc_fake_redirect'));

        $response = $controller->deleteprojectAction(1);

        $this->assertEquals($response,'codebender.cc_fake_redirect' );
    }


    public function testListFilenamesAction_showIno()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listFilesAction"))
            ->getMock();

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:list_filenames.html.twig'), $this->equalTo(array('files' => array(array('filename' => 'header.h', 'code' => ''), array('filename' => 'project.ino', 'code' => '')))))->will($this->returnValue('list_rendered'));

        $response = $controller->listFilenamesAction(1, true);

        $this->assertEquals($response, "list_rendered");
    }

    public function testListFilenamesAction_notIno()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listFilesAction"))
            ->getMock();

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project.ino","code":""}]}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:list_filenames.html.twig'), $this->equalTo(array('files' => array(array('filename' => 'header.h', 'code' => '')))))->will($this->returnValue('list_rendered'));

        $response = $controller->listFilenamesAction(1, false);

        $this->assertEquals($response, "list_rendered");
    }

    public function testChangePrivacyAction_Success_PublicToPrivate()
{
    $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

    $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
        ->disableOriginalConstructor()
        ->setMethods(array("getPrivacyAction", "setProjectPrivateAction"))
        ->getMock();

    $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
        ->disableOriginalConstructor()
        ->setMethods(array("getCurrentUserAction"))
        ->getMock();

    $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
        ->disableOriginalConstructor()
        ->setMethods(array('logAction'))
        ->getMock();

    $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
    $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
    $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
    $projectmanager->expects($this->once())->method('getPrivacyAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response":true}')));
    $projectmanager->expects($this->once())->method('setProjectPrivateAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));
    $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
    $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(8),$this->equalTo('CHANGE_PROJECT_PERMISSIONS'),$this->equalTo('{"success":true,"project":1,"from":"public","to":"private"}'));

    $response = $controller->changePrivacyAction(1);
    $this->assertEquals($response->getContent(), '{"success":true}');

}

    public function testChangePrivacyAction_Failure_PublicToPrivate()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getPrivacyAction", "setProjectPrivateAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $projectmanager->expects($this->once())->method('getPrivacyAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response":true}')));
        $projectmanager->expects($this->once())->method('setProjectPrivateAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(8),$this->equalTo('CHANGE_PROJECT_PERMISSIONS'),$this->equalTo('{"success":false,"project":1,"from":"public","to":"private"}'));

        $response = $controller->changePrivacyAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}');

    }

    public function testChangePrivacyAction_Success_PrivateToPublic()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getPrivacyAction", "setProjectPublicAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $projectmanager->expects($this->once())->method('getPrivacyAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response":false}')));
        $projectmanager->expects($this->once())->method('setProjectPublicAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(8),$this->equalTo('CHANGE_PROJECT_PERMISSIONS'),$this->equalTo('{"success":true,"project":1,"from":"private","to":"public"}'));

        $response = $controller->changePrivacyAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testChangePrivacyAction_Failure_PrivateToPublic()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getPrivacyAction", "setProjectPublicAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $projectmanager->expects($this->once())->method('getPrivacyAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response":false}')));
        $projectmanager->expects($this->once())->method('setProjectPublicAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null),$this->equalTo(8),$this->equalTo('CHANGE_PROJECT_PERMISSIONS'),$this->equalTo('{"success":false,"project":1,"from":"private","to":"public"}'));

        $response = $controller->changePrivacyAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}');

    }

	public function testRenderDescriptionAction_success()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getDescriptionAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('getDescriptionAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"response":"awesome project"}')));

        $response = $controller->renderDescriptionAction(1);
        $this->assertEquals($response->getContent(), "awesome project");
	}

    public function testRenderDescriptionAction_failure()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getDescriptionAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('getDescriptionAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->renderDescriptionAction(1);
        $this->assertEquals($response->getContent(), "Project description not found.");
    }

	public function testSetDescriptionAction_success()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("setDescriptionAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('new description'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('setDescriptionAction')->with($this->equalTo(1), $this->equalTo('new description'))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(13), $this->equalTo('CHANGE_PROJECT_DESCRIPTION'), '{"success":true,"project":1}');

        $response = $controller->setDescriptionAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetDescriptionAction_failure()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("setDescriptionAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('new description'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('setDescriptionAction')->with($this->equalTo(1), $this->equalTo('new description'))->will($this->returnValue(new Response('{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(13), $this->equalTo('CHANGE_PROJECT_DESCRIPTION'), '{"success":false,"project":1}');

        $response = $controller->setDescriptionAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}');
    }

    public function testSetNameAction_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("renameAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('new name'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('renameAction')->with($this->equalTo(1), $this->equalTo('new name'))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(14), $this->equalTo('CHANGE_PROJECT_NAME'), '{"success":true,"project":1}');

        $response = $controller->setNameAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetNameAction_failure()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("renameAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('new name'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('renameAction')->with($this->equalTo(1), $this->equalTo('new name'))->will($this->returnValue(new Response('{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(14), $this->equalTo('CHANGE_PROJECT_NAME'), '{"success":false,"project":1}');

        $response = $controller->setNameAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","project":1}');
    }

    public function testRenameFileAction_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("renameFileAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->exactly(2))->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->at(0))->method('get')->with($this->equalTo('oldFilename'))->will($this->returnValue('old name'));
        $req->expects($this->at(1))->method('get')->with($this->equalTo('newFilename'))->will($this->returnValue('new name'));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('old name'), $this->equalTo('new name'))->will($this->returnValue(new Response('{"success":true,"message":"File renamed successfully."}')));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(15), $this->equalTo('CHANGE_FILE_NAME'), '{"success":true,"project":1,"oldFilename":"old name","newFilename":"new name"}');

        $response = $controller->renameFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"File renamed successfully."}');
    }

    public function testRenameFileAction_failure()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("renameFileAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->exactly(2))->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->at(0))->method('get')->with($this->equalTo('oldFilename'))->will($this->returnValue('old name'));
        $req->expects($this->at(1))->method('get')->with($this->equalTo('newFilename'))->will($this->returnValue('new name'));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('old name'), $this->equalTo('new name'))->will($this->returnValue(new Response('{"success":false,"message":"File could not be renamed."}')));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(15), $this->equalTo('CHANGE_FILE_NAME'), '{"success":false,"project":1,"oldFilename":"old name","newFilename":"new name"}');
        $response = $controller->renameFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"File could not be renamed."}');
    }


	public function testSidebarAction()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get",'render'));

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("listAction"))
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));
        $projectmanager->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response('[{"id":1,"name":"Blink","description":"","is_public":true},{"id":2,"name":"project","description":"","is_public":true}]')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:sidebar.html.twig'), $this->equalTo(array('files' => array(array('id' => 1, "name"=>'Blink', 'description' => "", 'is_public'=> true), array('id' => 2, "name"=>'project', 'description' => "", 'is_public'=> true)))))->will($this->returnValue('rendered response'));

        $response = $controller->sidebarAction();
        $this->assertEquals($response, "rendered response");
	}

    public function testIntercomHashAction()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));
        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $container->expects($this->once())->method('getParameter')->with($this->equalTo('intercom_secret_key'))->will($this->returnValue('THis1sas3crETk3Y!'));
        $response = $controller->intercomHashAction(1);

        $this->assertEquals($response->getContent(),'298e50a0d8cbd91529047184c1a247e5fcda4734480295e5b43d2f1f566f4155');
    }

    public function testDownloadAction()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("getNameAction", 'listFilesAction'))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('getNameAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"response":"project "}')));
        $projectmanager->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header.h","code":""},{"filename":"project .ino","code":""}]}')));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(5),$this->equalTo('DOWNLOAD_PROJECT'),$this->equalTo('{"success":true,"project":1}'));
        $response = $controller->downloadAction(1);

        $this->assertEquals($response->headers->get('content_type'), "application/octet-stream");
        $this->assertEquals($response->headers->get('Content-Disposition'), 'attachment;filename="project-.zip"');
        $this->assertEquals($response->getStatusCode(), 200);

    }

    public function testDownloadExampleAction()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();


        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();


        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get"))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));
        $container->expects($this->once())->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('library/url/'));

        $handler->expects($this->once())->method('get')->with($this->equalTo('library/url/example/url'))->will($this->returnValue('{"success":true,"files":[{"filename":"example.ino","code":""}]}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(30),$this->equalTo('DOWNLOAD_LIBRARY_EXAMPLE'),$this->equalTo('{"success":true,"url":"example\/url"}'));

        $response = $controller->downloadExampleAction('project', 'example/url');


        $this->assertEquals($response->headers->get('content_type'), "application/octet-stream");
        $this->assertEquals($response->headers->get('Content-Disposition'), 'attachment;filename="project.zip"');
        $this->assertEquals($response->getStatusCode(), 200);



    }

    public function testSaveCodeAction_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateEditAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("setFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->once())->method('updateEditAction');

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req ->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"project.ino":"", "header.h":""}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('setFileAction')->with($this->equalTo(1), $this->equalTo('project.ino'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":true,"message":"Saved successfully."}')));
        $projectmanager->expects($this->at(1))->method('setFileAction')->with($this->equalTo(1), $this->equalTo('header.h'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":true,"message":"Saved successfully."}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(2),$this->equalTo('EDIT_PROJECT'),$this->equalTo('{"success":true,"project":1}'));

        $response = $controller->saveCodeAction(1);

        $this->assertEquals($response->getContent(), '{"success":true,"message":"Saved successfully."}');
    }

    public function testSaveCodeAction_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateEditAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("setFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->once())->method('updateEditAction');

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req ->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"project.ino":"", "header.h":""}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->at(0))->method('setFileAction')->with($this->equalTo(1), $this->equalTo('project.ino'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":true,"message":"Saved successfully."}')));
        $projectmanager->expects($this->at(1))->method('setFileAction')->with($this->equalTo(1), $this->equalTo('header.h'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":false,"filename":"header.h","error":"You have no permissions to the directory.","message":"Save failed."}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(2),$this->equalTo('EDIT_PROJECT'),$this->equalTo('{"success":false,"project":1}'));

        $response = $controller->saveCodeAction(1);

        $this->assertEquals($response->getContent(), '{"success":false,"filename":"header.h","error":"You have no permissions to the directory.","message":"Save failed."}');
    }

    public function testSaveCodeAction_noData()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req ->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue(null));

        $response = $controller->saveCodeAction(1);

        $this->assertEquals($response->getContent(), 'No data.');
        $this->assertEquals($response->getStatusCode(), 500);
    }

    public function testSaveCodeAction_notJson()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req ->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('not json'));

        $response = $controller->saveCodeAction(1);

        $this->assertEquals($response->getContent(), 'Wrong data.');
        $this->assertEquals($response->getStatusCode(), 500);
    }


	public function testCloneAction_success()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("cloneAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->once())->method('updateCloningAction');

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('name'))->will($this->returnValue('projectName'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('cloneAction')->with($this->equalTo(1), $this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "id":2}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(1),$this->equalTo('CLONE_PROJECT'),$this->equalTo('{"success":true,"project":2,"parent":1}'));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_project'), $this->equalTo(array('id' => 2)))->will($this->returnValue('project_url'));
        $controller->expects($this->once())->method("redirect")->with($this->equalTo('project_url'))->will($this->returnValue('fake project redirect'));

        $res = $controller->cloneAction(1);
        $this->assertEquals($res, 'fake project redirect');

	}

    public function testCloneAction_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("cloneAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $usercontroller->expects($this->once())->method('updateCloningAction');

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('name'))->will($this->returnValue('projectName'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('cloneAction')->with($this->equalTo(1), $this->equalTo(1))->will($this->returnValue(new Response('{"success":false, "id":1, "error": "Clone project failed."}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(1),$this->equalTo('CLONE_PROJECT'),$this->equalTo('{"success":false,"parent":1}'));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo('error'), $this->equalTo('Error: Clone project failed.'));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('homepage_url'));
        $controller->expects($this->once())->method("redirect")->with($this->equalTo('homepage_url'))->will($this->returnValue('fake homepage redirect'));

        $res = $controller->cloneAction(1);
        $this->assertEquals($res, 'fake homepage redirect');

    }

    public function testAddBoardAction_Error()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  1
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: Upload failed with error code 1."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":1}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_notTxt()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => ''
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: File type should be .txt."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"File type should be .txt."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_cannotAdd()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false,"error":"Cannot add personal board."}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: Cannot add personal board."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"Cannot add personal board."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_cannotParse()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction", "parsePropertiesFileAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain',
                'tmp_name' => '.'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"available":"1"}')));
        $boardsController->expects($this->once())->method('parsePropertiesFileAction')->with($this->equalTo(''))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: Could not read Board Properties File."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"Could not read Board Properties File."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_failCountBoards()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction", "parsePropertiesFileAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain',
                'tmp_name' => '.'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();
        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"available":"1"}')));
        $boardsController->expects($this->once())->method('parsePropertiesFileAction')->with($this->equalTo(''))->will($this->returnValue(new Response('{"success":true, "boards":[{"board1":""},{"board2":""}]}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: You can add up to 1 boards (tried to add 2)."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"You can add up to 1 boards (tried to add 2)"}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }


    public function testAddBoardAction_invalidBoard()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction", "parsePropertiesFileAction", "isValidBoardAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain',
                'tmp_name' => '.'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"available":"3"}')));
        $boardsController->expects($this->once())->method('parsePropertiesFileAction')->with($this->equalTo(''))->will($this->returnValue(new Response('{"success":true, "boards":[{"board1":""}]}')));
        $boardsController->expects($this->once())->method('isValidBoardAction')->with($this->equalTo(array("board1"=>"")))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: File does not have the required structure."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"File does not have the required structure."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_errorOnAdding()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction", "parsePropertiesFileAction", "isValidBoardAction", "addBoardAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain',
                'tmp_name' => '.'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"available":"3"}')));
        $boardsController->expects($this->once())->method('parsePropertiesFileAction')->with($this->equalTo(''))->will($this->returnValue(new Response('{"success":true, "boards":{"board1":{"name":"Arduino Blah"}}}')));
        $boardsController->expects($this->once())->method('isValidBoardAction')->with($this->equalTo(array("name" => "Arduino Blah")))->will($this->returnValue(new Response('{"success":true}')));
        $boardsController->expects($this->once())->method('addBoardAction')->with($this->equalTo(array("name" => "Arduino Blah")), $this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("error"), $this->equalTo("Error: Could not add board 'Arduino Blah'. Process stopped."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":false,"error":"Could not add board. Process stopped."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testAddBoardAction_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("canAddPersonalBoardAction", "parsePropertiesFileAction", "isValidBoardAction", "addBoardAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $_FILES = array(
            'boards'    =>  array(
                'error'     =>  0,
                'type'  => 'text/plain',
                'tmp_name' => '.'
            ));

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('canAddPersonalBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"available":"3"}')));
        $boardsController->expects($this->once())->method('parsePropertiesFileAction')->with($this->equalTo(''))->will($this->returnValue(new Response('{"success":true, "boards":{"board1":{"name":"Arduino Blah"}}}')));
        $boardsController->expects($this->once())->method('isValidBoardAction')->with($this->equalTo(array("name" => "Arduino Blah")))->will($this->returnValue(new Response('{"success":true}')));
        $boardsController->expects($this->once())->method('addBoardAction')->with($this->equalTo(array("name" => "Arduino Blah")), $this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo("notice"), $this->equalTo("1 boards were successfully added."));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(22),$this->equalTo('UPLOAD_BOARD'),$this->equalTo('{"success":true,"message":"1 boards were successfully added."}'));

        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo("CodebenderGenericBundle_boards"))->will($this->returnValue('boards url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo("boards url"))->will($this->returnValue('fake redirect boards url'));

        $res = $controller->addBoardAction();

        $this->assertEquals($res,'fake redirect boards url');

    }

    public function testDeleteBoard_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteBoardAction"))
            ->getMock();


        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('deleteBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"message":"Board Arduino Blah was successfully deleted."}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(23),$this->equalTo('DELETE_BOARD'),$this->equalTo('{"success":true}'));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo('notice'), $this->equalTo('Board Arduino Blah was successfully deleted.'));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_boards'))->will($this->returnValue('boards_url'));
        $controller->expects($this->once())->method("redirect")->with($this->equalTo('boards_url'))->will($this->returnValue('fake boards redirect'));

        $res = $controller->deleteBoardAction(1);
        $this->assertEquals($res, 'fake boards redirect');

    }


    public function testEditBoard_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("editAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->at(0))->method('get')->with($this->equalTo('id'))->will($this->returnValue(1));
        $req->expects($this->at(1))->method('get')->with($this->equalTo('desc'))->will($this->returnValue("description"));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('name'))->will($this->returnValue("Arduino Blah"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('editAction')->with($this->equalTo(1), $this->equalTo('Arduino Blah'), $this->equalTo('description'))->will($this->returnValue(new Response('{"success":true,"new_name":"Arduino Blah","new_desc":"description"}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(24), $this->equalTo('EDIT_BOARD'), $this->equalTo('{"success":true}'));

        $res = $controller->editBoardAction();
        $this->assertEquals($res->getContent(), '{"success":true,"new_name":"Arduino Blah","new_desc":"description"}');
    }

    public function testEditBoard_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("editAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->at(0))->method('get')->with($this->equalTo('id'))->will($this->returnValue(1));
        $req->expects($this->at(1))->method('get')->with($this->equalTo('desc'))->will($this->returnValue("description"));
        $req->expects($this->at(2))->method('get')->with($this->equalTo('name'))->will($this->returnValue("Arduino Blah"));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('editAction')->with($this->equalTo(1), $this->equalTo('Arduino Blah'), $this->equalTo('description'))->will($this->returnValue(new Response('{"success":false,"message":"Cannot edit board Arduino Blah."}')));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(24), $this->equalTo('EDIT_BOARD'), $this->equalTo('{"success":false}'));

        $res = $controller->editBoardAction();
        $this->assertEquals($res->getContent(), '{"success":false,"message":"Cannot edit board Arduino Blah."}');
    }


    public function testDeleteBoard_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "generateUrl", "redirect"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction", "updateCloningAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder("Codebender\BoardBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteBoardAction"))
            ->getMock();


        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashBag'))
            ->getMock();

        $flashBag = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Flash")
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));

        $boardsController->expects($this->once())->method('deleteBoardAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false,"message":"You have no permissions to delete this board."}')));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(23),$this->equalTo('DELETE_BOARD'),$this->equalTo('{"success":false,"error":"You have no permissions to delete this board."}'));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $session->expects($this->once())->method('getFlashBag')->will($this->returnValue($flashBag));
        $flashBag->expects($this->once())->method('add')->with($this->equalTo('error'), $this->equalTo('Error: You have no permissions to delete this board.'));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_boards'))->will($this->returnValue('boards_url'));
        $controller->expects($this->once())->method("redirect")->with($this->equalTo('boards_url'))->will($this->returnValue('fake boards redirect'));

        $res = $controller->deleteBoardAction(1);
        $this->assertEquals($res, 'fake boards redirect');

    }

	public function testCreateFileAction_success()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"filename":"name"}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo('name'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":true,"message":"File created successfully"}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(11), $this->equalTo('CREATE_FILE'), $this->equalTo('{"success":true,"message":"File created successfully"}'));

        $response = $controller->createFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"File created successfully"}');

	}

    public function testCreateFileAction_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("createFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"filename":"name"}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo('name'), $this->equalTo(''))->will($this->returnValue(new Response('{"success":false,"message":"File could not be created"}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(11), $this->equalTo('CREATE_FILE'), $this->equalTo('{"success":false,"message":"File could not be created"}'));

        $response = $controller->createFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"File could not be created"}');

    }

    public function testDeleteFileAction_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"filename":"name"}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue(new Response('{"success":true,"message":"File successfully deleted"}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(12), $this->equalTo('DELETE_FILE'), $this->equalTo('{"success":true,"message":"File successfully deleted"}'));

        $response = $controller->deleteFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"File successfully deleted"}');

    }

    public function testDeleteFileAction_fail()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectmanager = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->setMethods(array("deleteFileAction"))
            ->getMock();

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get", "getRequest"));

        $request->request = $req;

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $req->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue('{"filename":"name"}'));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($projectmanager));

        $projectmanager->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue(new Response('{"success":false,"message":"File could not be deleted"}')));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(12), $this->equalTo('DELETE_FILE'), $this->equalTo('{"success":false,"message":"File could not be deleted"}'));

        $response = $controller->deleteFileAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"File could not be deleted"}');

    }

	public function testImageAction()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();


        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get_gravatar"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"email":"girder@codebender.cc", "username":"girder"}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));
        $handler->expects($this->once())->method('get_gravatar')->with($this->equalTo('girder@codebender.cc'))->will($this->returnValue("//www.gravatar.com/avatar/blahblahblah"));
        $controller->expects($this->once())->method('render')->with($this->equalTo("CodebenderUtilitiesBundle:Default:image.html.twig"),$this->equalTo(array('user' => 'girder', 'image' => '//www.gravatar.com/avatar/blahblahblah')))->will($this->returnValue('rendered image'));

        $response = $controller->imageAction();

        $this->assertEquals($response, "rendered image");


	}

    //Not used any more
//    public function testCompileAction_syntax()
//    {
//        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "render"));
//
//        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
//            ->disableOriginalConstructor()
//            ->setMethods(array("getCurrentUserAction","updateCompileAction"))
//            ->getMock();
//
//        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
//            ->disableOriginalConstructor()
//            ->setMethods(array("get", "post_raw_data"))
//            ->getMock();
//
//        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
//            ->disableOriginalConstructor()
//            ->setMethods(array('logAction'))
//            ->getMock();
//
//        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
//            ->disableOriginalConstructor()
//            ->setMethods(array('getParameter'))
//            ->getMockForAbstractClass();
//
//        $controller->setContainer($container);
//
//        $controller->expects($this->once())->method('getRequest')->will($this->returnValue(new Response('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"syntax","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}' )));
//
//        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
//        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));
//        $usercontroller->expects($this->once())->method('updateCompileAction');
//        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(21), $this->equalTo('VERIFY_PROJECT'), $this->equalTo(''));
//        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
//        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));
//
//        $container->expects($this->at(0))->method('getParameter')->with($this->equalTo('library'))->will($this->returnValue('http://library/url'));
//
//        $handler->expects($this->at(0))->method('get')->with($this->equalTo('http://library/url/fetch?library=header'))->will($this->returnValue('{"success":true,"message":"Library found","files":[{"filename":"header.h","content":""}]}'));
//
//        $container->expects($this->at(1))->method('getParameter')->with($this->equalTo('compiler'))->will($this->returnValue('http://compiler/url'));
//
//        $handler->expects($this->once())->method('post_raw_data')->with($this->equalTo('http://compiler/url'), $this->equalTo('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"syntax","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"libraries":{"header":[{"filename":"header.h","content":""}]}}')) -> will($this->returnValue('compiler response'));
//
//
//        $response = $controller->compileAction();
//
//        $this->assertEquals($response->getContent(), 'compiler response');
//
//    }

    public function testCompileAction_binary()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateCompileAction"))
            ->getMock();

        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get", "post_raw_data"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue(new Response('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"binary","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}' )));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $usercontroller->expects($this->once())->method('updateCompileAction');
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(6), $this->equalTo('COMPILE_PROJECT'), $this->equalTo(''));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));

        $container->expects($this->at(1))->method('getParameter')->with($this->equalTo('compiler'))->will($this->returnValue('http://compiler/url'));

        $handler->expects($this->once())->method('post_raw_data')->with($this->equalTo('http://compiler/url'), $this->equalTo('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"binary","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"libraries":[]]}}')) -> will($this->returnValue('compiler response'));


        $response = $controller->compileAction();

        $this->assertEquals($response->getContent(), 'compiler response');

    }

    public function testCompileAction_hex()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateCompileAction"))
            ->getMock();

        $handler = $this->getMockBuilder("Codebender\UtilitiesBundle\Handler\DefaultHandler")
            ->disableOriginalConstructor()
            ->setMethods(array("get", "post_raw_data"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue(new Response('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"hex","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}' )));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $usercontroller->expects($this->once())->method('updateCompileAction');
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(26), $this->equalTo('DOWNLOAD_HEX'), $this->equalTo(''));
        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($handler));

        $container->expects($this->at(1))->method('getParameter')->with($this->equalTo('compiler'))->will($this->returnValue('http://compiler/url'));

        $handler->expects($this->once())->method('post_raw_data')->with($this->equalTo('http://compiler/url'), $this->equalTo('{"files":[{"filename":"project.ino","content":"#include <header.h> #include \"header2.h\" "}],"format":"hex","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"libraries":[]]}}')) -> will($this->returnValue('compiler response'));


        $response = $controller->compileAction();

        $this->assertEquals($response->getContent(), 'compiler response');

    }

    public function testCompileAction_wrongReq()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue(new Response('not_json' )));

        $response = $controller->compileAction();

        $this->assertEquals($response->getContent(), 'Wrong data.');
        $this->assertEquals($response->getStatusCode(), 500);
    }

    public function testCompileAction_noData()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "getRequest", "render"));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('getParameter'))
            ->getMockForAbstractClass();

        $controller->setContainer($container);

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue(new Response(null)));

        $response = $controller->compileAction();

        $this->assertEquals($response->getContent(), 'No data.');
        $this->assertEquals($response->getStatusCode(), 500);
    }


    public function testFlashAction_loggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(7), $this->equalTo('FLASH_PROJECT'), $this->equalTo(''));
        $usercontroller->expects($this->once())->method('updateFlashAction');

        $response = $controller->flashAction();
        $this->assertEquals($response->getContent(), "OK");
    }

    public function testFlashAction_NotloggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));
        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(7), $this->equalTo('FLASH_PROJECT'), $this->equalTo(''));
        $usercontroller->expects($this->once())->method('updateFlashAction');

        $response = $controller->flashAction();
        $this->assertEquals($response->getContent(), "OK");
    }

    public function testDownloadHexAction_null()
    {
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("getRequest"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('hex'))->will($this->returnValue(null));

        $response = $controller->downloadHexAction();
        $this->assertEquals($response->getContent(), "No data.");
        $this->assertEquals($response->getStatusCode(), 500);
    }

    public function testDownloadHexAction_notJson()
    {
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("getRequest"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('hex'))->will($this->returnValue('not json'));

        $response = $controller->downloadHexAction();
        $this->assertEquals($response->getContent(), "No data.");
        $this->assertEquals($response->getStatusCode(), 500);
    }

    public function testDownloadHexAction_success()
    {
        $request = $this->getMockBuilder("Symfony\Component\HttpFoundation\Request")
            ->disableOriginalConstructor()
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("getRequest"));

        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('hex'))->will($this->returnValue('{"hex":"0123456789ABCDEF"}'));

        $response = $controller->downloadHexAction();
        $this->assertEquals($response->getContent(), "0123456789ABCDEF");
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->headers->get('content_type'), "application/octet-stream");
        $this->assertEquals($response->headers->get('Content-Disposition'), 'attachment;filename="project.hex"');
    }

    public function testIntercomAction_loggedIn_nullUserProperties()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"preregistration_date":null,"registration_date":null,"last_edit":null,"last_compile":null,"last_flash":null,"last_cloning":null,"actual_last_login":null,"last_walkthrough_date":null}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:intercom.html.twig'), $this->equalTo(array('user' => array("success" => true, "id" => 1,'preregistration_date' => 1352246400,'registration_date' => null,'last_edit' => null,'last_compile' => null,'last_flash' => null,'last_cloning' => null,'actual_last_login' => null,'last_walkthrough_date' => null))))->will($this->returnValue('rendered intercom'));

        $response = $controller->intercomAction();
        $this->assertEquals($response, "rendered intercom");
    }

    public function testIntercomAction_loggedIn_notnNullUserProperties()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"last_walkthrough_date":{"date":"2014-01-13 11:17:23","timezone_type":3,"timezone":"Europe\/Berlin"},"preregistration_date":{"date":"2012-12-04 19:01:07","timezone_type":3,"timezone":"Europe\/Berlin"},"registration_date":{"date":"2013-12-23 19:01:07","timezone_type":3,"timezone":"Europe\/Berlin"},"actual_last_login":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"edit_count":72,"last_edit":{"date":"2014-01-16 18:06:56","timezone_type":3,"timezone":"Europe\/Berlin"},"compile_count":617,"last_compile":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"flash_count":273,"last_flash":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"cloning_count":12,"last_cloning":{"date":"2014-01-10 12:18:46","timezone_type":3,"timezone":"Europe\/Berlin"}}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:intercom.html.twig'), $this->equalTo(array('user' => array("success" => true, "id" => 1,'preregistration_date' => 1354644067, 'registration_date' =>  1387821667,'last_edit' => 1389892016,'last_compile' => 1390228951,'last_flash' => 1390228951,'last_cloning' => 1389352726,'actual_last_login' => 1390228951,'last_walkthrough_date' => 1389608243,'edit_count' => 72, 'compile_count' => 617, 'flash_count' => 273, 'cloning_count' => 12))))->will($this->returnValue('rendered intercom'));

        $response = $controller->intercomAction();
        $this->assertEquals($response, "rendered intercom");
    }

    public function testIntercomAction_loggedIn_nullPreReg()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"last_walkthrough_date":{"date":"2014-01-13 11:17:23","timezone_type":3,"timezone":"Europe\/Berlin"},"preregistration_date":{"date":"null","timezone_type":3,"timezone":"Europe\/Berlin"},"registration_date":{"date":"2013-12-23 19:01:07","timezone_type":3,"timezone":"Europe\/Berlin"},"actual_last_login":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"edit_count":72,"last_edit":{"date":"2014-01-16 18:06:56","timezone_type":3,"timezone":"Europe\/Berlin"},"compile_count":617,"last_compile":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"flash_count":273,"last_flash":{"date":"2014-01-20 15:42:31","timezone_type":3,"timezone":"Europe\/Berlin"},"cloning_count":12,"last_cloning":{"date":"2014-01-10 12:18:46","timezone_type":3,"timezone":"Europe\/Berlin"}}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:intercom.html.twig'), $this->equalTo(array('user' => array("success" => true, "id" => 1,'preregistration_date' => 1352246400, 'registration_date' =>  1387821667,'last_edit' => 1389892016,'last_compile' => 1390228951,'last_flash' => 1390228951,'last_cloning' => 1389352726,'actual_last_login' => 1390228951,'last_walkthrough_date' => 1389608243,'edit_count' => 72, 'compile_count' => 617, 'flash_count' => 273, 'cloning_count' => 12))))->will($this->returnValue('rendered intercom'));

        $response = $controller->intercomAction();
        $this->assertEquals($response, "rendered intercom");
    }


    public function testIntercomAction_notloggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));


        $response = $controller->intercomAction();
        $this->assertEquals($response->getContent(), "");
    }


    public function testOlarkAction_loggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:olark.html.twig'), $this->equalTo(array('user' => array("success" => true, "id" => 1))))->will($this->returnValue('rendered olark'));

        $response = $controller->olarkAction();
        $this->assertEquals($response, "rendered olark");
    }

    public function testOlarkAction_notloggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));


        $response = $controller->olarkAction();
        $this->assertEquals($response->getContent(), "");
    }

    public function testWalkthroughEulaModalAction_loggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderUtilitiesBundle:Default:walkthroughEulaModal.html.twig'), $this->equalTo(array('user' => array("success" => true, "id" => 1))))->will($this->returnValue('rendered walkthrough modal'));

        $response = $controller->walkthroughEulaModalAction();
        $this->assertEquals($response, "rendered walkthrough modal");
    }

    public function testWalkthroughEulaModalAction_notloggedIn()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get","render"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));


        $response = $controller->walkthroughEulaModalAction();
        $this->assertEquals($response->getContent(), "");
    }

	public function testLogAction()
	{
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", NULL);
        $response = $controller->logAction(1);

        $this->assertEquals($response->getContent(), 'OK');
	}

    public function testLogDatabaseAction_CloudFlash()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(16), $this->equalTo('CLOUD_FLASH_BUTTON'), "");
        $response = $controller->logDatabaseAction(16,'');
        $this->assertEquals($response->getContent(), "OK");

    }

    public function testLogDatabaseAction_WebSerialMonitor()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(17), $this->equalTo('WEBSERIAL_MONITOR_BUTTON'), "");
        $response = $controller->logDatabaseAction(17,'');
        $this->assertEquals($response->getContent(), "OK");

    }

    public function testLogDatabaseAction_SerialMonitor()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(18), $this->equalTo('SERIAL_MONITOR_BUTTON'), "");
        $response = $controller->logDatabaseAction(18,'');
        $this->assertEquals($response->getContent(), "OK");

    }

    public function testLogDatabaseAction_UploadBootloader()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(25), $this->equalTo('UPLOAD_BOOTLOADER_BUTTON'), "");
        $response = $controller->logDatabaseAction(25,'');
        $this->assertEquals($response->getContent(), "OK");

    }

    public function testLogDatabaseAction_PluginInfo()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(27), $this->equalTo('PLUGIN_INFO'), "");
        $response = $controller->logDatabaseAction(27,'');
        $this->assertEquals($response->getContent(), "OK");

    }
    public function testLogDatabaseAction_OsBrowserInfo()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $controller->expects($this->at(1))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(28), $this->equalTo('OS_BROWSER_INFO'), "");
        $response = $controller->logDatabaseAction(28,'');
        $this->assertEquals($response->getContent(), "OK");

    }

    public function testLogDatabaseAction_invalid()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction","updateFlashAction"))
            ->getMock();


        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1}')));

        $response = $controller->logDatabaseAction(1,'');
        $this->assertEquals($response->getContent(), "Invalid Action ID");

    }

    public function testAcceptEulaAtion_success()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get", "generateUrl", "redirect"));
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("setEulaAction"))
            ->getMock();

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('setEulaAction')->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->once())->method('generateUrl')->with($this->equalTo('CodebenderGenericBundle_index'))->will($this->returnValue('hompeage_url'));
        $controller->expects($this->once())->method('redirect')->with($this->equalTo('hompeage_url'))->will($this->returnValue('fake_redirect'));

        $response = $controller->acceptEulaAction();
        $this->assertEquals($response, "fake_redirect");

    }

    public function testAcceptEulaAtion_failure()
    {
        $controller = $this->getMock("Codebender\UtilitiesBundle\Controller\DefaultController", array("get"));
        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("setEulaAction"))
            ->getMock();

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));

        $usercontroller->expects($this->once())->method('setEulaAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->acceptEulaAction();
        $this->assertEquals($response->getContent(), '');

    }
}
