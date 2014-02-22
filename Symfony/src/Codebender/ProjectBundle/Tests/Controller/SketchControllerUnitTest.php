<?php

namespace Codebender\ProjectBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Codebender\ProjectBundle\Controller\SketchController;
use Symfony\Component\Security\Acl\Exception\Exception;

class SketchControllerPrivateTester extends SketchController
{
    public function call_canCreateFile($id, $filename)
    {
        return $this->canCreateFile($id, $filename);
    }

    public function call_inoExists($id)
    {
        return $this->inoExists($id);
    }
}

class SketchControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    protected $project;

    //---createprojectAction
    public function testCreateprojectAction_CanCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject","createFileAction", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":true}')));
        $response = $controller->createprojectAction(1,"projectName", "code", false);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePrivateFromParent()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project."}'));


        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project."}');
    }

    public function testCreateprojectAction_CannotCreatePrivateFile()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject","createFileAction", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}')));
        $response = $controller->createprojectAction(1,"projectName", "code", false);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}');
    }

    public function testCreateprojectAction_CanCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction", "createFileAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":true}')));

        $response = $controller->createprojectAction(1,"projectName", "code", true);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePublicFromParent()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":false,"owner_id":1,"name":"projectName"}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":false,"owner_id":1,"name":"projectName"}');
    }

    public function testCreateprojectAction_CannotCreatePublicFile()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction", "createFileAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}')));

        $response = $controller->createprojectAction(1,"projectName", "code", true);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}');
    }

    //---cloneAction
    public function testCloneAction_Yes()
    {
        $new_project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction", "createFileAction", "checkReadProjectPermissions"));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->at(1))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $controller->expects($this->at(2))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->at(3))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name copy'))->will($this->returnValue('{"success":false}'));

        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue('des'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1), $this->equalTo('name copy'), $this->equalTo('des'), $this->equalTo(true))->will($this->returnValue(new Response( '{"success":true,"id":2}')));

        $controller->expects($this->at(5))->method('getProjectById')->with($this->equalTo(2))->will($this->returnValue($new_project));
        $this->project->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));

        $new_project->expects($this->once())->method('setParent')->with($this->equalTo(1));
        $em->expects($this->once())->method('persist')->with($this->equalTo($new_project));
        $em->expects($this->once())->method('flush');

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"name.ino","code":"void setup(){}void loop(){}"},{"filename":"header.h","code":"function f(){}"}]}')));

        $controller->expects($this->at(7))->method('createFileAction')->with($this->equalTo(2), $this->equalTo("name copy.ino"), $this->equalTo("void setup(){}void loop(){}"));
        $controller->expects($this->at(8))->method('createFileAction')->with($this->equalTo(2), $this->equalTo("header.h"), $this->equalTo("function f(){}"));
        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(), '{"success":true,"id":2}');

    }

    public function testCloneAction_No()
    {
        $new_project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction", 'checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->at(1))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $controller->expects($this->at(2))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->at(3))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name copy'))->will($this->returnValue('{"success":false}'));

        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue('des'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1), $this->equalTo('name copy'), $this->equalTo('des'), $this->equalTo(true))->will($this->returnValue(new Response( '{"success":false,"owner_id":1,"name":"name copy"}')));

        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(),'{"success":false,"id":1,"error":"Clone project failed."}');
    }

    public function testCloneAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }

    //---renameAction
    public function testRenameAction_validName()
    {
        $project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid", "getProjectById", "listFilesAction", "renameFileAction",'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('newproject'))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->at(2))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
        $project->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"project.ino","code":"void function1(){}"},{"filename":"header2.h","code":"function2(){}"}]}')));
        $controller->expects($this->at(4))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
        $project->expects($this->once())->method('getName')->will($this->returnValue('project'));

        $controller->expects($this->at(5))->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('project.ino'), $this->equalTo('newproject.ino.bkp'))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(6))->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('newproject.ino.bkp'), $this->equalTo('newproject.ino'))->will($this->returnValue(new Response('{"success":true}')));



        $project->expects($this->once())->method('setName')->with($this->equalTo('newproject'));

        $em->expects($this->once())->method('persist')->with($this->equalTo($project));
        $em->expects($this->once())->method('flush');
        $response = $controller->renameAction(1, 'newproject');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testRenameAction_NoInvalidName()
    {
        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid",'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('invalid/name'))->will($this->returnValue('{"success":false,"error":"Invalid Name. Please enter a new one."}'));

        $response = $controller->renameAction(1, 'invalid/name');
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Invalid Name. Please enter a new one."}');
    }

    public function testRenameAction_NoOldFile()
{
    $project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
        ->disableOriginalConstructor()
        ->getMock();

    $controller = $this->setUpController($em, $fc, $security, array("nameIsValid", "getProjectById", "listFilesAction", "renameFileAction",'checkWriteProjectPermissions'));
    $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
    $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('newproject'))->will($this->returnValue('{"success":true}'));

    $controller->expects($this->at(2))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
    $project->expects($this->once())->method('getId')->will($this->returnValue(1));
    $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"project.ino","code":"void function1(){}"},{"filename":"header2.h","code":"function2(){}"}]}')));
    $controller->expects($this->at(4))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
    $project->expects($this->once())->method('getName')->will($this->returnValue('project'));

    $controller->expects($this->at(5))->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('project.ino'), $this->equalTo('newproject.ino.bkp'))->will($this->returnValue(new Response('{"success":false, "error": "fake_error"}')));
    $response = $controller->renameAction(1, 'newproject');
    $this->assertEquals($response->getContent(), '{"success":false,"error":"old file project.ino could not be renamed. fake_error"}');
}

    public function testRenameAction_NoBackupFile()
    {
        $project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid", "getProjectById", "listFilesAction", "renameFileAction",'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));

        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('newproject'))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->at(2))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
        $project->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"project.ino","code":"void function1(){}"},{"filename":"header2.h","code":"function2(){}"}]}')));
        $controller->expects($this->at(4))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($project));
        $project->expects($this->once())->method('getName')->will($this->returnValue('project'));

        $controller->expects($this->at(5))->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('project.ino'), $this->equalTo('newproject.ino.bkp'))->will($this->returnValue(new Response('{"success":true}')));
        $controller->expects($this->at(6))->method('renameFileAction')->with($this->equalTo(1), $this->equalTo('newproject.ino.bkp'), $this->equalTo('newproject.ino'))->will($this->returnValue(new Response('{"success":false, "error": "fake_error"}')));
        $response = $controller->renameAction(1, 'newproject');
        $this->assertEquals($response->getContent(), '{"success":false,"error":"backup file newproject.ino.bkp could not be renamed. fake_error"}');
    }

    public function testRenameAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->renameAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    public function testCanCreateFile_YesIno()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));

        $controller->expects($this->once())->method('inoExists')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":".ino file does not exists"}'));
        $response = $controller->call_canCreateFile(1, 'filename.ino');
        $this->assertEquals($response, '{"success":true}');
    }

    public function testCanCreateFile_YesNotIno()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));
        $response = $controller->call_canCreateFile(1, 'filename.h');
        $this->assertEquals($response, '{"success":true}');
    }

    public function testCanCreateFile_No()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));

        $controller->expects($this->once())->method('inoExists')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $response = $controller->call_canCreateFile(1, 'filename.ino');
        $this->assertEquals($response, '{"success":false,"id":1,"filename":"filename.ino","error":"Cannot create second .ino file in the same project"}');
    }

    public function testInoExists_No()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("listFilesAction"));

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"header1.h","code":"void function1(){}"},{"filename":"header2.h","code":"function2(){}"}]}')));
        $response = $controller->call_inoExists(1);
        $this->assertEquals($response, '{"success":false,"error":".ino file does not exist."}');
    }

    public function testInoExists_Yes()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("listFilesAction"));

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"project.ino","code":"void function1(){}"},{"filename":"header2.h","code":"function2(){}"}]}')));
        $response = $controller->call_inoExists(1);
        $this->assertEquals($response, '{"success":true}');
    }

    public function testInoExists_NoPermission()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("listFilesAction"));

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":false}')));
        $response = $controller->call_inoExists(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot access list of project files."}');
    }


    public function testConstructorInvalidStorageLayer()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $ffc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\DiskFilesController')
            ->disableOriginalConstructor()
            ->getMock();

        $thrown = false;
        try
        {
        $controller = $this->getMock('Codebender\ProjectBundle\Controller\SketchController', $methods = NULL, $arguments = array($em, $ffc, $security, 'invalid'));
        }
        catch(\Exception $e)
        {
           if($e->getMessage() == 'Invalid Storage Layer')
           $thrown = true;
        }

        $this->assertEquals($thrown, true);
    }

    protected function setUp()
    {
        $this->project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function setUpController(&$em, &$fc, &$security, $m)
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $ffc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\DiskFilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $controller = $this->getMock('Codebender\ProjectBundle\Controller\SketchController', $methods = $m, $arguments = array($em, $ffc, $security, 'disk'));
        return $controller;
    }

    private function setUpTesterController(&$em, &$fc, &$security, $m)
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $ffc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\DiskFilesController')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\SketchControllerPrivateTester', $methods = $m, $arguments = array($em, $ffc, $security, 'mongo'));
        return $controller;
    }
}
