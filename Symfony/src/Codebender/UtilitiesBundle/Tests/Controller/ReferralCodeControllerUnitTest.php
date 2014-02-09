<?php

namespace Codebender\UtilitiesBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\Request;
use Codebender\UtilitiesBundle\Controller\ReferralCodeController;

class ReferralCodeControllerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testValidCode()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->getMock();

		$code = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\ReferralCode')
			->disableOriginalConstructor()
			->getMock();

		$code->expects($this->once())->method('getCode')->will($this->returnValue("valid_code"));
		$code->expects($this->once())->method('getIssued')->will($this->returnValue(50));
		$code->expects($this->exactly(2))->method('getAvailable')->will($this->returnValue(50));
		$code->expects($this->once())->method('setAvailable')->with($this->equalTo(49));
		$code->expects($this->once())->method('getPoints')->will($this->returnValue(50));

		$repo->expects($this->once())->method('findAll')->will($this->returnValue(array($code)));

		$em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
		$em->expects($this->once())->method('persist')->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush')->will($this->returnValue($repo));

		$controller = new ReferralCodeController($em);
		$response = $controller->useCodeAction("valid_code");
		$this->assertEquals($response->getContent(), '{"success":true,"points":50}');
	}

	public function testUnknownCode()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->getMock();

		$code = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\ReferralCode')
			->disableOriginalConstructor()
			->getMock();

		$code->expects($this->once())->method('getCode')->will($this->returnValue("valid_code"));

		$repo->expects($this->once())->method('findAll')->will($this->returnValue(array($code)));

		$em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

		$controller = new ReferralCodeController($em);
		$response = $controller->useCodeAction("invalid_code");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testAvailableZero()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->getMock();

		$code = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\ReferralCode')
			->disableOriginalConstructor()
			->getMock();

		$code->expects($this->once())->method('getCode')->will($this->returnValue("valid_code"));
		$code->expects($this->once())->method('getIssued')->will($this->returnValue(50));
		$code->expects($this->once())->method('getAvailable')->will($this->returnValue(0));

		$repo->expects($this->once())->method('findAll')->will($this->returnValue(array($code)));

		$em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

		$controller = new ReferralCodeController($em);
		$response = $controller->useCodeAction("valid_code");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testIssuedNULL()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->getMock();

		$code = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\ReferralCode')
			->disableOriginalConstructor()
			->getMock();

		$code->expects($this->once())->method('getCode')->will($this->returnValue("valid_code"));
		$code->expects($this->once())->method('getIssued')->will($this->returnValue(null));
		$code->expects($this->once())->method('getPoints')->will($this->returnValue(50));

		$repo->expects($this->once())->method('findAll')->will($this->returnValue(array($code)));

		$em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));
		$em->expects($this->once())->method('persist')->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush')->will($this->returnValue($repo));

		$controller = new ReferralCodeController($em);
		$response = $controller->useCodeAction("valid_code");
		$this->assertEquals($response->getContent(), '{"success":true,"points":50}');
	}

	public function testNoCode()
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->getMock();

		$repo->expects($this->once())->method('findAll')->will($this->returnValue(array()));

		$em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

		$controller = new ReferralCodeController($em);
		$response = $controller->useCodeAction("valid_code");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}
}
