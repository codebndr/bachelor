<?php

namespace Codebender\BoardBundle\Tests\Controller;

use Codebender\BoardBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerPrivateTester extends DefaultController
{
    public function call_canAddPersonalBoard($user_id)
    {
        return $this->canAddPersonalBoard($user_id);
    }

    public function call_checkBoardPermissions($id)
    {
        return $this->checkBoardPermissions($id);
    }

    public function call_getBoardById($id)
    {
        return $this->getBoardById($id);
    }
}

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testListBoards_NotLoggedIn()
	{

        $controller = $this->setUpController($em, $security, $container, array('get'));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
            ->setMethods(array('findBy'))
			->getMock();

		$board = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\Board')
			->disableOriginalConstructor()
            ->setMethods(array('getName', 'getUpload', 'getBootloader', 'getBuild', 'getDescription', 'getId'))
			->getMock();

        $controller->expects($this->once())->method('get')->with('codebender_user.usercontroller')->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));


        $em->expects($this->once())->method('getRepository')->will($this->returnValue($repo));

        $repo->expects($this->once())->method('findBy')->with($this->equalTo(array("owner" => null)))->will($this->returnValue(array($board)));

        $board->expects($this->once())->method('getName')->will($this->returnValue("Arduino Skata"));
		$board->expects($this->once())->method('getUpload')->will($this->returnValue('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}'));
		$board->expects($this->once())->method('getBootloader')->will($this->returnValue('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}'));
		$board->expects($this->once())->method('getBuild')->will($this->returnValue('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}'));
		$board->expects($this->once())->method('getDescription')->will($this->returnValue("KAI GAMW TA ARDUINA"));
        $board->expects($this->once())->method('getId')->will($this->returnValue(1));


		$response = $controller->listAction();
		$this->assertEquals($response->getContent(), '[{"name":"Arduino Skata","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"description":"KAI GAMW TA ARDUINA","personal":false,"id":1}]');
	}

    /**
     * @runInSeparateProcess
     */
    public function testListBoards_LoggedInNoPersonal()
    {

        $controller = $this->setUpController($em, $security, $container, array('get'));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy', 'findByOwner'))
            ->getMock();

        $board = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'getUpload', 'getBootloader', 'getBuild', 'getDescription', 'getId'))
            ->getMock();

        $controller->expects($this->once())->method('get')->with('codebender_user.usercontroller')->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));


        $em->expects($this->exactly(2))->method('getRepository')->will($this->returnValue($repo));

        $repo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array()));

        $repo->expects($this->once())->method('findBy')->with($this->equalTo(array("owner" => null)))->will($this->returnValue(array($board)));

        $board->expects($this->once())->method('getName')->will($this->returnValue("Arduino Skata"));
        $board->expects($this->once())->method('getUpload')->will($this->returnValue('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}'));
        $board->expects($this->once())->method('getBootloader')->will($this->returnValue('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}'));
        $board->expects($this->once())->method('getBuild')->will($this->returnValue('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}'));
        $board->expects($this->once())->method('getDescription')->will($this->returnValue("KAI GAMW TA ARDUINA"));
        $board->expects($this->once())->method('getId')->will($this->returnValue(1));


        $response = $controller->listAction();
        $this->assertEquals($response->getContent(), '[{"name":"Arduino Skata","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"description":"KAI GAMW TA ARDUINA","personal":false,"id":1}]');
    }

    /**
     * @runInSeparateProcess
     */
    public function testListBoards_LoggedInHasPersonal()
    {

        $controller = $this->setUpController($em, $security, $container, array('get'));

        $usercontroller = $this->getMockBuilder("Codebender\UserBundle\Controller\DefaultController")
            ->disableOriginalConstructor()
            ->setMethods(array("getCurrentUserAction"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findBy', 'findByOwner'))
            ->getMock();

        $personalboard = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'getUpload', 'getBootloader', 'getBuild', 'getDescription', 'getId'))
            ->getMock();

        $board = $this->getMockBuilder('Codebender\UtilitiesBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->setMethods(array('getName', 'getUpload', 'getBootloader', 'getBuild', 'getDescription', 'getId'))
            ->getMock();

        $controller->expects($this->once())->method('get')->with('codebender_user.usercontroller')->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true, "id":1}')));


        $em->expects($this->exactly(2))->method('getRepository')->will($this->returnValue($repo));

        $repo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($personalboard)));

        $repo->expects($this->once())->method('findBy')->with($this->equalTo(array("owner" => null)))->will($this->returnValue(array($board)));

        $board->expects($this->once())->method('getName')->will($this->returnValue("Arduino Skata"));
        $board->expects($this->once())->method('getUpload')->will($this->returnValue('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}'));
        $board->expects($this->once())->method('getBootloader')->will($this->returnValue('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}'));
        $board->expects($this->once())->method('getBuild')->will($this->returnValue('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}'));
        $board->expects($this->once())->method('getDescription')->will($this->returnValue("KAI GAMW TA ARDUINA"));
        $board->expects($this->once())->method('getId')->will($this->returnValue(1));

        $personalboard->expects($this->once())->method('getName')->will($this->returnValue("Arduino Pesonal"));
        $personalboard->expects($this->once())->method('getUpload')->will($this->returnValue('{"protocol":"arduino","maximum_size":"32256","speed":"115200"}'));
        $personalboard->expects($this->once())->method('getBootloader')->will($this->returnValue('{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"}'));
        $personalboard->expects($this->once())->method('getBuild')->will($this->returnValue('{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}'));
        $personalboard->expects($this->once())->method('getDescription')->will($this->returnValue("wow"));
        $personalboard->expects($this->once())->method('getId')->will($this->returnValue(2));


        $response = $controller->listAction();
        $this->assertEquals($response->getContent(), '[{"name":"Arduino Pesonal","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"description":"wow","personal":true,"id":2},{"name":"Arduino Skata","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"},"description":"KAI GAMW TA ARDUINA","personal":false,"id":1}]');
    }

    public function testCreateBoardsPlanAction_Yes()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, NULL);


        $em->expects($this->once())->method("getRepository")->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($user));

        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $response = $controller->createBoardsPlanAction('1','des','2012-12-08',null,2);
        $this->assertEquals($response->getContent(), '{"success":true,"id":null}');


    }

    public function testEditAction_Yes()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"true"}'));

        $board->expects($this->once())->method('setName')->with($this->equalTo('name'));
        $board->expects($this->once())->method('setDescription')->with($this->equalTo('des'));

        $em->expects($this->once())->method('persist')->with($this->equalTo($board));
        $em->expects($this->once())->method('flush');

        $response = $controller->editAction(1,'name', 'des');

        $this->assertEquals($response->getContent(), '{"success":true,"new_name":"name","new_desc":"des"}');

    }

    public function testEditAction_NoPermissions()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"false","error","You have no permissions for this board","id":1}'));
        $board->expects($this->once())->method('getName')->will($this->returnValue('name'));

        $response = $controller->editAction(1,'name', 'des');

        $this->assertEquals($response->getContent(), '{"success":false,"message":"Cannot edit board \'name\'."}');

    }

    public function testSetNameAction_Yes()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"true"}'));

        $board->expects($this->once())->method('setName')->with($this->equalTo('name'));


        $em->expects($this->once())->method('persist')->with($this->equalTo($board));
        $em->expects($this->once())->method('flush');

        $response = $controller->setNameAction(1,'name');

        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testSetNameAction_NoPermissions()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"false","error","You have no permissions for this board","id":1}'));
        $board->expects($this->once())->method('getName')->will($this->returnValue('name'));

        $response = $controller->setNameAction(1,'name');

        $this->assertEquals($response->getContent(), '{"success":false,"message":"Cannot set name for board \'name\'."}');

    }


    public function testSetDescriptionAction_Yes()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"true"}'));

        $board->expects($this->once())->method('setDescription')->with($this->equalTo('des'));


        $em->expects($this->once())->method('persist')->with($this->equalTo($board));
        $em->expects($this->once())->method('flush');

        $response = $controller->setDescriptionAction(1,'des');

        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testSetDescriptionAction_NoPermissions()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, array("getBoardById",'checkBoardPermissions'));

        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));
        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"false","error","You have no permissions for this board","id":1}'));
        $board->expects($this->once())->method('getName')->will($this->returnValue('name'));

        $response = $controller->setDescriptionAction(1,'des');

        $this->assertEquals($response->getContent(), '{"success":false,"message":"Cannot set description for board \'name\'."}');

    }

    public function testAddBoardAction_Yes()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $security, $container, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo("CodebenderUserBundle:User"))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($user));

        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $response = $controller->addBoardAction(array('name'=>'theName', 'upload'=>'theUpload', 'bootloader' => 'theBootloader', 'build'=>'theBuild'),1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testIsValidBoardAction_Yes()
    {
        $board = array('name'=>'theName', 'upload'=>'theUpload', 'bootloader' => 'theBootloader', 'build'=>'theBuild');
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction($board);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testIsValidBoardAction_NoName()
    {
        $board = array('upload'=>'theUpload', 'bootloader' => 'theBootloader', 'build'=>'theBuild');
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction($board);
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testIsValidBoardAction_NoUpload()
    {
        $board = array('name'=>'theName', 'bootloader' => 'theBootloader', 'build'=>'theBuild');
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction($board);
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testIsValidBoardAction_NoBootloader()
    {
        $board = array('name'=>'theName', 'upload'=>'theUpload', 'build'=>'theBuild');
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction($board);
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testIsValidBoardAction_NoBuild()
    {
        $board = array('name'=>'theName', 'upload'=>'theUpload', 'bootloader' => 'theBootloader');
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction($board);
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testIsValidBoardAction_NullInput()
    {
        $controller = $this->setUpController($em, $security, $container, NULL);
        $response = $controller->isValidBoardAction(null);
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testDeleteBoardAction_Yes()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->setUpController($em, $security, $container, array('checkBoardPermissions', 'getBoardById'));

        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"true"}'));
        $controller->expects($this->once())->method('getBoardById')->with($this->equalTo(1))->will($this->returnValue($board));

        $em->expects($this->once())->method('remove')->with($this->equalTo($board));
        $em->expects($this->once())->method('flush');

        $board->expects($this->once())->method('getName')->will($this->returnValue('name'));

        $response = $controller->deleteBoardAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"Board \'name\' was successfully deleted."}');
    }

    public function testDeleteBoardAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $security, $container, array('checkBoardPermissions', 'getBoardById'));

        $controller->expects($this->once())->method('checkBoardPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":"false","error","You have no permissions for this board","id":1}'));

        $response = $controller->deleteBoardAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"You have no permissions to delete this board."}');
    }

    public function testCanAddPersonalBoardAction_Yes()
    {
        $controller = $this->setUpController($em, $security, $containter, array('canAddPersonalBoard'));

        $controller->expects($this->once())->method('canAddPersonalBoard')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"available":2}'));

        $response = $controller->canAddPersonalBoardAction(1);
        $this->assertEquals($response->getContent(),'{"success":true,"available":2}');
    }

    public function testCanAddPersonalBoardAction_No()
    {
        $controller = $this->setUpController($em, $security, $containter, array('canAddPersonalBoard'));

        $controller->expects($this->once())->method('canAddPersonalBoard')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot add personal board}'));

        $response = $controller->canAddPersonalBoardAction(1);
        $this->assertEquals($response->getContent(),'{"success":false,"error":"Cannot add personal board}');
    }

    public function testParsePropertiesFileAction_YesOneBoard()
    {
        $controller = $this->setUpController($em, $security, $containter, null);
        $response = $controller->parsePropertiesFileAction(
            "uno.name=Arduino Uno
uno.upload.protocol=arduino
uno.upload.maximum_size=32256
uno.upload.speed=115200
uno.bootloader.low_fuses=0xff
uno.bootloader.high_fuses=0xde
uno.bootloader.extended_fuses=0x05
uno.bootloader.path=optiboot
uno.bootloader.file=optiboot_atmega328.hex
uno.bootloader.unlock_bits=0x3F
uno.bootloader.lock_bits=0x0F
uno.build.mcu=atmega328p
uno.build.f_cpu=16000000L
uno.build.core=arduino
uno.build.variant=standard"
            );

        $this->assertEquals($response->getContent(),'{"success":true,"boards":{"uno":{"name":"Arduino Uno","upload":{"protocol":"arduino","maximum_size":"32256","speed":"115200"},"bootloader":{"low_fuses":"0xff","high_fuses":"0xde","extended_fuses":"0x05","path":"optiboot","file":"optiboot_atmega328.hex","unlock_bits":"0x3F","lock_bits":"0x0F"},"build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}}}');
    }


    public function testParsePropertiesFileAction()
    {
        $this->markTestIncomplete('Not fully tested yet. Needs improvements.');
    }

    public function testCanAddPersonalBoard_YesOneValidOneExpired()
    {
        $boardRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prsRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $board1 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $board2 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($boardRepo));
        $boardRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($board1, $board2)));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:PersonalBoards'))->will($this->returnValue($prsRepo));
        $prsRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($expired, $valid)));


        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('3102-01-01')));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(4));

        $response = $controller->call_canAddPersonalBoard(1);
        $this->assertEquals($response, '{"success":true,"available":2}');
    }

    public function testCanAddPersonalBoard_YesOneNeverExpiresOneExpired()
    {
        $boardRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prsRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $board1 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $board2 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($boardRepo));
        $boardRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($board1, $board2)));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:PersonalBoards'))->will($this->returnValue($prsRepo));
        $prsRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($expired, $valid)));


        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->once())->method('getExpires')->will($this->returnValue(null));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(4));

        $response = $controller->call_canAddPersonalBoard(1);
        $this->assertEquals($response, '{"success":true,"available":2}');
    }

    public function testCanAddPersonalBoard_NoHaveNoPersonalBoards()
    {
        $boardRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prsRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();


        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($boardRepo));
        $boardRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array()));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:PersonalBoards'))->will($this->returnValue($prsRepo));
        $prsRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array()));


        $response = $controller->call_canAddPersonalBoard(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot add personal board."}');
    }

    public function testCanAddPersonalBoard_NoAlreadyHasMany()
    {
        $boardRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prsRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $board1 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $board2 = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\BoardBundle\Entity\PersonalBoards')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($boardRepo));
        $boardRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($board1, $board2)));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:PersonalBoards'))->will($this->returnValue($prsRepo));
        $prsRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($expired, $valid)));


        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('3102-01-01')));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(2));

        $response = $controller->call_canAddPersonalBoard(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot add personal board."}');
    }

    public function testCheckBoardPermissions_Yes()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->setUpPrivateTesterController($em, $security, $container, array('getBoardById'));

        $controller->expects($this->once())->method('getBoardById')-> with($this->equalTo(1))->will($this->returnValue($board));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $board->expects($this->exactly(2))->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->exactly(2))->method('getId')->will($this->returnValue(10));

        $response = $controller->call_checkBoardPermissions(1);
        $this->assertEquals($response, '{"success":true}');

    }

    public function testCheckBoardPermissions_NoNotOwner()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $owner = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, array('getBoardById'));

        $controller->expects($this->once())->method('getBoardById')-> with($this->equalTo(1))->will($this->returnValue($board));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $board->expects($this->exactly(2))->method('getOwner')->will($this->returnValue($owner));
        $user->expects($this->once())->method('getId')->will($this->returnValue(10));
        $owner->expects($this->once())->method('getId')->will($this->returnValue(12));

        $response = $controller->call_checkBoardPermissions(1);
        $this->assertEquals($response, '{"success":false,"error":"You have no permissions for this board.","id":1}');

    }

    public function testCheckBoardPermissions_NoNotLoggedIn()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = "anon.";

        $owner = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, array('getBoardById'));

        $controller->expects($this->once())->method('getBoardById')-> with($this->equalTo(1))->will($this->returnValue($board));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $board->expects($this->once())->method('getOwner')->will($this->returnValue($owner));

        $response = $controller->call_checkBoardPermissions(1);
        $this->assertEquals($response, '{"success":false,"error":"You have no permissions for this board.","id":1}');

    }

    public function testGetBoardById_Exists()
    {
        $board = $this->getMockBuilder('Codebender\BoardBundle\Entity\Board')
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($board));

        $response = $controller->call_getBoardById(1);
        $this->assertEquals($response, $board);

    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */

    public function testGetBoardById_DoesNotExist()
    {
        $board = null;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $security, $container, null);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderBoardBundle:Board'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($board));

         $controller->call_getBoardById(1);
    }

    private function setUpController(&$em, &$security, &$container, $m)
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\BoardBundle\Controller\DefaultController', $methods = $m, $arguments = array($em, $security, $container));
        return $controller;
    }

    private function setUpPrivateTesterController(&$em, &$security, &$container, $m)
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\BoardBundle\Tests\Controller\DefaultControllerPrivateTester', $methods = $m, $arguments = array($em, $security, $container));
        return $controller;
    }
}
