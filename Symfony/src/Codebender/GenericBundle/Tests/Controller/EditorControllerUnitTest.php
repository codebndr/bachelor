<?php

namespace Codebender\GenericBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Codebender\GenericBundle\Controller\EditorController;
use Codebender\UtilitiesBundle\Controller\BoardController;

class EditorControllerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testEditAction_NoPerms()
	{
		$this->initParameters($em, $df, $sc);
		/** @var SketchController $projectmanager */
		$projectmanager = $this->getMock("Codebender\ProjectBundle\Controller\SketchController", array("checkWriteProjectPermissionsAction"), array($em, $df, $sc, "disk"));
		$projectmanager->expects($this->once())->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));

		/** @var EditorController $controller */
		$controller = $this->getMock("Codebender\GenericBundle\Controller\EditorController", array("get", "forward"));
		$controller->expects($this->once())->method('get')->with($this->equalTo("codebender_project.sketchmanager"))->will($this->returnValue($projectmanager));
		$controller->expects($this->once())->method('forward')->with($this->equalTo("CodebenderGenericBundle:Default:project"), $this->equalTo(array("id" => 1)))->will($this->returnValue(new Response("forwarded_response")));

		$response = $controller->editAction(1);
		$this->assertEquals($response->getContent(), "forwarded_response");
	}

	public function testEditAction_Success()
	{
		$this->initParameters($em, $df, $sc);
		/** @var SketchController $projectmanager */
		$projectmanager = $this->getMock("Codebender\ProjectBundle\Controller\SketchController", array("checkWriteProjectPermissionsAction", "getNameAction", "getPrivacyAction", "listFilesAction"), array($em, $df, $sc, "disk"));
		$projectmanager->expects($this->once())->method('checkWriteProjectPermissionsAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true}')));
		$projectmanager->expects($this->once())->method('getNameAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response": "test_project"}')));
		$projectmanager->expects($this->once())->method('getPrivacyAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "response": true}')));
		$projectmanager->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true, "list":[{"filename":"test_project.ino", "code":"nothing"}]}')));

        $container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');
		/** @var BoardController $boardcontroller */
		$boardcontroller = $this->getMock("Codebender\BoardBundle\Controller\DefaultController", array("listAction"), array($em, $sc, $container));
		$boardcontroller->expects($this->once())->method('listAction')->will($this->returnValue(new Response('fake_boards_list')));

		/** @var EditorController $controller */
		$controller = $this->getMock("Codebender\GenericBundle\Controller\EditorController", array("get", "forward", "render"));
		$controller->expects($this->at(0))->method('get')->with($this->equalTo("codebender_project.sketchmanager"))->will($this->returnValue($projectmanager));
		$controller->expects($this->at(1))->method('get')->with($this->equalTo("codebender_board.defaultcontroller"))->will($this->returnValue($boardcontroller));
		$controller->expects($this->once())->method('render')->with($this->equalTo('CodebenderGenericBundle:Editor:editor.html.twig'), $this->equalTo(array('project_id' => 1, 'project_name' => "test_project", 'files' => array(array("filename" => "test_project.ino", "code" => "nothing")), 'boards' => "fake_boards_list", 'is_public' => true)))->will($this->returnValue(new Response("excellent")));

		$response = $controller->editAction(1);
		$this->assertEquals($response->getContent(), "excellent");
	}

	private function initParameters(&$em, &$df, &$sc)
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$df = $this->getMockBuilder('Codebender\ProjectBundle\Controller\DiskFilesController')
			->disableOriginalConstructor()
			->getMock();

		$sc = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
			->disableOriginalConstructor()
			->getMock();
	}
}
