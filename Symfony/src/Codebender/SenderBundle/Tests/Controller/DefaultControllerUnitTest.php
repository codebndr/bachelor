<?php

namespace Codebender\SenderBundle\Tests\Controller;

class DefaultControllerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testTftpActionDataCorrect()
	{
		$data = json_encode(array("ip" => "127.0.0.1", "bin" => "fake_binary"));

		$response_query = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));
		$response_query->expects($this->once())->method('get')->with($this->equalTo('data'))->will($this->returnValue($data));

		$request = $this->getMock("Symfony\Component\HttpFoundation\Request");
		$request->request = $response_query;

		$container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$container->expects($this->once())->method('getParameter')->with($this->equalTo('sender'))->will($this->returnValue("http://fake.sender.com"));

		/** @var SenderController $controller */
		$controller = $this->getMock("Codebender\SenderBundle\Controller\DefaultController", array("getRequest"));
		$controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
		$controller->setContainer($container);

		$utilities_handler = $this->getMock("Codebender\UtilitiesBundle\Handler\DefaultHandler", array("get_data"));
		$utilities_handler->expects($this->once())->method('get_data')
			->with($this->equalTo("http://fake.sender.com"),
				$this->equalTo("bin"),
				$this->equalTo("fake_binary&ip=127.0.0.1"))
			->will($this->returnValue("some_data"));

		$response = $controller->tftpAction($utilities_handler);
		$this->assertEquals($response->getContent(), '"some_data"');
	}

	public function testTftpActionDataNotSet()
	{
		$data = json_encode(array("ip" => "127.0.0.1"));
		$data2 = json_encode(array("bin" => "fake_binary"));

		$response_query = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));
		$response_query->expects($this->at(0))->method('get')->with($this->equalTo('data'))->will($this->returnValue($data));
		$response_query->expects($this->at(1))->method('get')->with($this->equalTo('data'))->will($this->returnValue($data2));

		$request = $this->getMock("Symfony\Component\HttpFoundation\Request");
		$request->request = $response_query;

		/** @var SenderController $controller */
		$controller = $this->getMock("Codebender\SenderBundle\Controller\DefaultController", array("getRequest"));
		$controller->expects($this->exactly(2))->method('getRequest')->will($this->returnValue($request));

		$response = $controller->tftpAction();
		$this->assertEquals($response->getContent(), '{"success":false,"output":"no ip or binary was set"}');

		$response = $controller->tftpAction();
		$this->assertEquals($response->getContent(), '{"success":false,"output":"no ip or binary was set"}');
	}

	public function testTftpActionDataFalse()
	{
		$data = json_encode(array("ip" => "127.0.0.1", "bin" => false));
		$data2 = json_encode(array("ip" => 0, "bin" => "fake_binary"));

		$response_query = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));
		$response_query->expects($this->at(0))->method('get')->with($this->equalTo('data'))->will($this->returnValue($data));
		$response_query->expects($this->at(1))->method('get')->with($this->equalTo('data'))->will($this->returnValue($data2));

		$request = $this->getMock("Symfony\Component\HttpFoundation\Request");
		$request->request = $response_query;

		/** @var SenderController $controller */
		$controller = $this->getMock("Codebender\SenderBundle\Controller\DefaultController", array("getRequest"));
		$controller->expects($this->exactly(2))->method('getRequest')->will($this->returnValue($request));

		$response = $controller->tftpAction();
		$this->assertEquals($response->getContent(), '{"success":false,"output":"ip or binary was false and\/or zero"}');

		$response = $controller->tftpAction();
		$this->assertEquals($response->getContent(), '{"success":false,"output":"ip or binary was false and\/or zero"}');
	}
}
