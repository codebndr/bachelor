<?php

namespace Codebender\ProjectBundle\Tests\Controller;

use Codebender\ProjectBundle\Controller\ProjectController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class ProjectControllerPrivateTester extends ProjectController
{
    public function call_canCreatePrivateProject($owner)
    {
        return $this->canCreatePrivateProject($owner);
    }

    public function call_canCreateFile($id, $filename)
    {
        return $this->canCreateFile($id, $filename);
    }

    public function call_nameIsValid($name)
    {
        return $this->nameIsValid($name);
    }

    public function call_checkReadProjectPermissions($id)
    {
        return $this->checkReadProjectPermissions($id);
    }
    public function call_checkWriteProjectPermissions($id)
    {
        return $this->checkWriteProjectPermissions($id);
    }
    public function call_nameExists($owner, $name)
    {
        return $this->nameExists($owner,$name);
    }

}

class ProjectControllerUnitTest extends \PHPUnit_Framework_TestCase
{

    protected $project;

    //---createprojectAction
    public function testCreateprojectAction_CanCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project."}'));


        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project."}');
    }

    public function testCreateprojectAction_CanCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":false,"owner_id":1,"name":"projectName"}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":false,"owner_id":1,"name":"projectName"}');
    }

	//---listAction
	public function testListAction_Empty()
	{
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = 'anon.';

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with('CodebenderProjectBundle:Project')->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findByOwner')->with(1)->will($this->returnValue(array()));
        $response = $controller->listAction(1);
        $this->assertEquals($response->getContent(), "[]");
	}

    public function testListAction_NotLoggedIn()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("checkReadProjectPermissions"));


        $em->expects($this->once())->method('getRepository')->with('CodebenderProjectBundle:Project')->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findByOwner')->with(1)->will($this->returnValue(array($private,$public)));

        $private->expects($this->once())->method('getId')->will($this->returnValue(2));
        $controller->expects($this->at(0))->method('checkReadProjectPermissions')->with(2)->will($this->returnValue('{"success":false,"message":"Read permissions not granted","error":"You have no read permissions for this project.","id":2}'));

        $public->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $controller->expects($this->at(1))->method('checkReadProjectPermissions')->with(1)->will($this->returnValue('{"success":true,"message":"Read permissions granted"}'));

        $public->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $public->expects($this->once())->method('getDescription')->will($this->returnValue("description"));

        $response = $controller->listAction(1);
        $this->assertEquals($response->getContent(), '[{"id":1,"name":"name","description":"description","is_public":true}]');
    }

    public function testListAction_NotOwner()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));

        $em->expects($this->once())->method('getRepository')->with('CodebenderProjectBundle:Project')->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findByOwner')->with(1)->will($this->returnValue(array($private,$public)));

        $private->expects($this->once())->method('getId')->will($this->returnValue(2));
        $controller->expects($this->at(0))->method('checkReadProjectPermissions')->with(2)->will($this->returnValue('{"success":false,"message":"Read permissions not granted","error":"You have no read permissions for this project.","id":2}'));

        $public->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $controller->expects($this->at(1))->method('checkReadProjectPermissions')->with(1)->will($this->returnValue('{"success":true,"message":"Read permissions granted"}'));


        $public->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $public->expects($this->once())->method('getDescription')->will($this->returnValue("description"));

        $response = $controller->listAction(1);
        $this->assertEquals($response->getContent(), '[{"id":1,"name":"name","description":"description","is_public":true}]');
    }

    public function testListAction_Owner()
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));

        $em->expects($this->once())->method('getRepository')->with('CodebenderProjectBundle:Project')->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findByOwner')->with(1)->will($this->returnValue(array($private,$public)));

        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $private->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));

        $controller->expects($this->at(0))->method('checkReadProjectPermissions')->with(2)->will($this->returnValue('{"success":true,"message":"Read permissions granted"}'));
        $controller->expects($this->at(1))->method('checkReadProjectPermissions')->with(1)->will($this->returnValue('{"success":true,"message":"Read permissions granted"}'));

        $public->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));
        $public->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $public->expects($this->once())->method('getDescription')->will($this->returnValue("description"));

        $private->expects($this->exactly(2))->method('getId')->will($this->returnValue(2));
        $private->expects($this->once())->method('getName')->will($this->returnValue("prvName"));
        $private->expects($this->once())->method('getDescription')->will($this->returnValue("prvDescription"));

        $response = $controller->listAction(1);
        $this->assertEquals($response->getContent(),'[{"id":2,"name":"prvName","description":"prvDescription","is_public":false},{"id":1,"name":"name","description":"description","is_public":true}]');
    }
	//---createAction
    public function testCreateAction_Yes()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();
        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid"));
        $controller->expects($this->once())->method("nameIsValid")->with($this->equalTo("projectName"))->will($this->returnValue('{"success":true}'));

        $em->expects($this->once())->method("getRepository")->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($user));
        $fc->expects($this->once())->method('createAction')->will($this->returnValue('{"success":true,"id":1234567890}'));
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $response = $controller->createAction(1,'projectName','des',true);
        $this->assertEquals($response->getContent(), '{"success":true,"id":null}');
    }
    public function testCreateAction_No()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();
        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array("getId"))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid"));
        $controller->expects($this->once())->method("nameIsValid")->with($this->equalTo("projectName"))->will($this->returnValue('{"success":true}'));

        $em->expects($this->once())->method("getRepository")->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($user));
        $fc->expects($this->once())->method('createAction')->will($this->returnValue('{"success":false,"message":"Project could not be created.","error":"Cannot create project."}'));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $response = $controller->createAction(1,'projectName','des',true);
        $this->assertEquals($response->getContent(), '{"success":false,"owner_id":1,"name":"projectName","error":"Cannot create project.","message":"Project could not be created."}');
    }
    public function testCreateAction_InvalidName()
    {

        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid"));
        $controller->expects($this->once())->method("nameIsValid")->with($this->equalTo("projectName"))->will($this->returnValue('{"success":false,"error":"Invalid Name. Please enter a new one."}'));

        $response = $controller->createAction(1,'projectName','des',true);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Invalid Name. Please enter a new one."}');
    }


	//---deleteAction
    public function testDeleteAction_CanDelete()
    {

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById", 'checkWriteProjectPermissions'));

        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));


        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $fc->expects($this->once())->method('deleteAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":true}'));
        $em->expects($this->once())->method('remove')->with($this->equalTo($this->project ));
        $em->expects($this->once())->method('flush');


        $response = $controller->deleteAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testDeleteAction_CannotDelete()
    {

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById", 'checkWriteProjectPermissions'));

        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $this->project->expects($this->exactly(2))->method('getProjectfilesId')->will($this->returnValue(1234567890));


        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $fc->expects($this->once())->method('deleteAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":false,"error":"No projectfiles found with id: 1234567890"}'));

        $response = $controller->deleteAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1234567890}');
    }

    public function testDeleteAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->deleteAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

	//---cloneAction
	public function testCloneAction_Yes()
	{
        $new_project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction","checkReadProjectPermissions"));
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

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"name.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}')));
        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(), '{"success":true,"id":2,"list":[{"filename":"name.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}],"name":"name copy"}');
	}

    public function testCloneAction_No()
    {
        $new_project = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction","checkReadProjectPermissions"));
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
        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid", "getProjectById", "listFilesAction","checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('valid name'))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}')));

        $response = $controller->renameAction(1, 'valid name');
        $this->assertEquals($response->getContent(), '{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}');
    }

    public function testRenameAction_invalidName()
    {
        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid","checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('invalid/name'))->will($this->returnValue('{"success":false,"error":"Invalid Name. Please enter a new one."}'));

        $response = $controller->renameAction(1, 'invalid/name');
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Invalid Name. Please enter a new one."}');
    }

    public function testRenameAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->renameAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---setProjectPublic
    public function testSetProjectPublicAction_OK()
    {
        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('setIsPublic')->with($this->equalTo(true));
        $em->expects($this->once())->method('persist')->with($this->equalTo($this->project));
        $em->expects($this->once())->method('flush');

        $response = $controller->setProjectPublicAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetProjectPublicAction_AlreadyPublic()
    {

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));

        $response = $controller->setProjectPublicAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"This project is already public."}');
    }

    public function testSetProjectPublicAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->setProjectPublicAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---setProjectPrivate
    public function testSetProjectPrivateAction_OK()
    {

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById", "canCreatePrivateProject", "checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $this->project->expects($this->once())->method('setIsPublic')->with($this->equalTo(false));
        $em->expects($this->once())->method('persist')->with($this->equalTo($this->project));
        $em->expects($this->once())->method('flush');

        $response = $controller->setProjectPrivateAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetProjectPrivateAction_AlreadyPrivate()
    {
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById", "canCreatePrivateProject", "checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));

        $response = $controller->setProjectPrivateAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"This project is already private."}');
    }

    public function testSetProjectPrivateAction_CannotCreatePrivate()
    {
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById", "canCreatePrivateProject", "checkWriteProjectPermissions"));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project"}'));

        $response = $controller->setProjectPrivateAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project"}');
    }

    public function testSetProjectPrivateAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->setProjectPrivateAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---getNameAction
    public function testGetNameAction_Exists()
    {

        $this->project->expects($this->once())->method('getName')->will($this->returnValue("projectName"));
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $response = $controller->getNameAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":"projectName"}');

    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */

    public function testGetNameAction_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));

        $controller->getNameAction(1);

    }

    public function testGetNameAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getNameAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }

    //---getParentAction
    public function testGetParentAction_DoesNotExist()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getParent')->will($this->returnValue(NULL));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    public function testGetParentAction_HasBeenDeleted()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkExistsAction','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getParent')->will($this->returnValue(2));
        $controller->expects($this->once())->method('checkExistsAction')->with($this->equalTo(2))->will($this->returnValue(new Response('{"success":false}')));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":false}');

    }
    public function testGetParentAction_Exists()
    {

        $parent  = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkExistsAction','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));

        $controller->expects($this->at(1))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getParent')->will($this->returnValue(2));
        $controller->expects($this->once())->method('checkExistsAction')->with($this->equalTo(2))->will($this->returnValue(new Response('{"success":true}')));

        $controller->expects($this->at(3))->method('getProjectById')->with($this->equalTo(2))->will($this->returnValue($parent));

        $parent->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getUsername')->will($this->returnValue('mthrfck'));
        $parent->expects($this->once())->method('getName')->will($this->returnValue('projectName'));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":{"id":2,"owner":"mthrfck","name":"projectName"}}');
    }

    public function testGetParentAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }
    //---getOwnerAction
    public function testGetOwnerAction()
    {
        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();



        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue('1'));
        $user->expects($this->once())->method('getUsername')->will($this->returnValue('mthrfck'));
        $user->expects($this->once())->method('getFirstname')->will($this->returnValue('John'));
        $user->expects($this->once())->method('getLastname')->will($this->returnValue('Doe'));
        $response = $controller->getOwnerAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}');
    }

    public function testGetOwnerAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getOwnerAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }

    //---getDescriptionAction
    public function testGetDescriptionAction()
    {

        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue("description"));
        $respone = $controller->getDescriptionAction(1);
        $this->assertEquals($respone->getContent(), '{"success":true,"response":"description"}');

    }
    public function testGetDescriptionAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getDescriptionAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }

    //---getPrivacyAction
    public function testGetPrivacyAction_public()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $respone = $controller->getPrivacyAction(1);
        $this->assertEquals($respone->getContent(), '{"success":true,"response":true}');
    }

    public function testGetPrivacyAction_private()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));
        $respone = $controller->getPrivacyAction(1);
        $this->assertEquals($respone->getContent(), '{"success":true,"response":false}');
    }

    public function testGetPrivacyAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getPrivacyAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }


    //---setDescriptionAction
    public function testSetDescriptionAction()
    {

        $controller = $this->setUpController($em, $fc, $security, array('getProjectById','checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('setDescription')->with($this->equalTo("newDescription"));

        $em->expects($this->once())->method('persist')->with($this->equalTo($this->project ));
        $em->expects($this->once())->method('flush');

        $respone = $controller->setDescriptionAction(1, 'newDescription');
        $this->assertEquals($respone->getContent(), '{"success":true}');

    }

    public function testSetDescriptionAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->setDescriptionAction(1, 'newDescription');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---listFilesAction
    public function testListFilesAction_HasPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));

        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));

        $fc->expects($this->once())->method('listFilesAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}'));

        $response = $controller->listFilesAction(1);

        $this->assertEquals($response->getContent(), '{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}');

    }

    public function testListFilesAction_HasNoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false}'));


        $response = $controller->listFilesAction(1);

        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    //---createFileAction
    public function testCreateFileAction_canCreate()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'canCreateFile', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));

        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('filename'))->will($this->returnValue('{"success":true}'));

        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));

        $fc->expects($this->once())->method('createFileAction')->with($this->equalTo(1234567890), $this->equalTo('filename'), $this->equalTo('void setup(){}'))->will($this->returnValue('{"success":true}'));

        $response = $controller->createFileAction(1, 'filename', 'void setup(){}');

        $this->assertEquals($response->getContent(), '{"success":true}');
    }


    public function testCreateFileAction_cannotCreate()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'canCreateFile', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));

        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('filename'))->will($this->returnValue('{"success":false}'));

        $response = $controller->createFileAction(1, 'filename', 'void setup(){}');

        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testCreateFileAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->createFileAction(1, 'filename', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---getFileAction
    public function testGetFileAction_canGet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('getFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":false}'));
        $response = $controller->getFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testGetFileAction_cannotGet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read Permissions Granted."}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('getFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":true,"code":"void setup(){}"}'));
        $response = $controller->getFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":true,"code":"void setup(){}"}');
    }
    public function testGetFileAction_NoPermissions()
    {

        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->getFileAction(1, 'name');;
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read Permissions Not Granted.","error":"You have no read permissions for this project.","id":1}');
    }

    //---setFileAction
    public function testSetFileAction_canSet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('setFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'),$this->equalTo('void setup(){}'))->will($this->returnValue('{"success":true}'));
        $response = $controller->setFileAction(1, 'name', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetFileAction_cannotSet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('setFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'),$this->equalTo('void setup(){}'))->will($this->returnValue('{"success":false}'));
        $response = $controller->setFileAction(1, 'name', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":false}');
    }
    public function testSetFileAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->setFileAction(1, 'name', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---deleteFileAction
    public function testDeleteFileAction_canDelete()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $response = $controller->deleteFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testDeleteFileAction_cannotDelete()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":false,"filename":"name","error":"File name does not exist}'));
        $response = $controller->deleteFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false,"filename":"name","error":"File name does not exist}');
    }
    public function testDeleteFileAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->deleteFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }

    //---renameFileAction
    public function testRenameFileAction_canRename()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions', 'canCreateFile'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('new'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('renameFileAction')->with($this->equalTo(1234567890),$this->equalTo('old'),$this->equalTo('new'))->will($this->returnValue('{"success":true}'));
        $response = $controller->renameFileAction(1, 'old', 'new');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testRenameFileAction_cannotRename_oldDoesNotExist()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkWriteProjectPermissions', 'canCreateFile'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('new'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('renameFileAction')->with($this->equalTo(1234567890),$this->equalTo('old'),$this->equalTo('new'))->will($this->returnValue('{"success":false,"filename":"old","error":"File old does not exist}'));
        $response = $controller->renameFileAction(1, 'old', 'new');
        $this->assertEquals($response->getContent(), '{"success":false,"filename":"old","error":"File old does not exist}');
    }

    public function testRenameFileAction_cannotRename_newIsIno()
    {
        $controller = $this->setUpController($em, $fc, $security, array('canCreateFile', 'checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write Permissions Granted."}'));
        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('new.ino'))->will($this->returnValue('{"success":false,"id":1,"filename":"new.ino","error":"Cannot create second .ino file in the same project"}'));
        $response = $controller->renameFileAction(1, 'old', 'new.ino');
        $this->assertEquals($response->getContent(), '{"success":false,"id":1,"filename":"new.ino","error":"Cannot create second .ino file in the same project"}');
    }


    public function testRenameFileAction_NoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->renameFileAction(1, 'old', 'new');
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write Permissions Not Granted.","error":"You have no write permissions for this project.","id":1}');
    }


    //---searchAction
	public function testSearchAction_NameOnly()
    {
        $controller = $this->setUpController($em, $fc, $security, array('searchNameAction', 'searchDescriptionAction'));


        $controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response( '{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}')));
        $controller->expects($this->once())->method('searchDescriptionAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response( '[]')));

        $response = $controller->searchAction("search_string");
        $this->assertEquals($response->getContent(), '{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}'
        );
    }

    public function testSearchAction_DescriptionOnly()
    {
        $controller = $this->setUpController($em, $fc, $security, array('searchNameAction', 'searchDescriptionAction'));


        $controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response( '[]')));
        $controller->expects($this->once())->method('searchDescriptionAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}')));

        $response = $controller->searchAction("search_string");
        $this->assertEquals($response->getContent(), '{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}'
        );
    }

    public function testSearchAction_NameAndDescription()
    {
        $controller = $this->setUpController($em, $fc, $security, array('searchNameAction', 'searchDescriptionAction'));


        $controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"7":{"name":"project","description":"awesome","owner":{"id":"2","username":"me","firstname":"Morgan","lastname":"Freeman"}}}')));
        $controller->expects($this->once())->method('searchDescriptionAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}},"7":{"name":"project","description":"awesome","owner":{"id":"2","username":"me","firstname":"Morgan","lastname":"Freeman"}}}')));

        $response = $controller->searchAction("search_string");
        $this->assertEquals($response->getContent(), '{"7":{"name":"project","description":"awesome","owner":{"id":"2","username":"me","firstname":"Morgan","lastname":"Freeman"}},"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}'
        );
    }

	//---searchNameAction
	public function testSearchNameAction_NoneExists()
	{
        $projects = array();

        $query = $this->getMockBuilder('MyQuery')
            ->disableOriginalConstructor()
            ->setMethods(array("getResult", "setMaxResults"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("createQueryBuilder"))
            ->getMock();

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array("where", "getQuery", "setParameter"))
            ->getMock();


        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));


        $repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('p'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('where')->with($this->equalTo('p.name LIKE :token'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('setParameter')->with($this->equalTo('token'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->once())->method('getResult')->will($this->returnValue($projects));
        $query->expects($this->once())->method('setMaxResults')->with(1000)->will($this->returnValue($query));;


        $response = $controller->searchNameAction("search_string");
        $this->assertEquals($response->getContent(), '[]');
	}

    public function testSearchNameAction_TwoExistOneAccessible()
    {
        $notAccessible = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $accessible = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projects = array($notAccessible, $accessible);

        $query = $this->getMockBuilder('MyQuery')
            ->disableOriginalConstructor()
            ->setMethods(array("getResult", "setMaxResults"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("createQueryBuilder"))
            ->getMock();

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array("where", "getQuery", "setParameter"))
            ->getMock();


        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions', 'getOwnerAction'));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));


        $repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('p'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('where')->with($this->equalTo('p.name LIKE :token'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('setParameter')->with($this->equalTo('token'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->once())->method('getResult')->will($this->returnValue($projects));
        $query->expects($this->once())->method('setMaxResults')->with(1000)->will($this->returnValue($query));

        $notAccessible->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->at(0))->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false}'));

        $accessible->expects($this->exactly(3))->method('getId')->will($this->returnValue(2));
        $controller->expects($this->at(1))->method('checkReadProjectPermissions')->with($this->equalTo(2))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getOwnerAction')->with($this->equalTo(2))->will($this->returnValue(new Response('{"success":true,"response":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}')));

        $accessible->expects($this->once())->method('getName')->will($this->returnValue('Name'));
        $accessible->expects($this->once())->method('getDescription')->will($this->returnValue('Description'));

        $response = $controller->searchNameAction("search_string");
        $this->assertEquals($response->getContent(), '{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}');
    }

	//---searchDescriptionAction
	public function testSearchDescriptionAction_NoneExists()
    {
        $projects = array();

        $query = $this->getMockBuilder('MyQuery')
            ->disableOriginalConstructor()
            ->setMethods(array("getResult", "setMaxResults"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("createQueryBuilder"))
            ->getMock();

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array("where", "getQuery", "setParameter"))
            ->getMock();


        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));


        $repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('p'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('where')->with($this->equalTo('p.description LIKE :token'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('setParameter')->with($this->equalTo('token'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->once())->method('getResult')->will($this->returnValue($projects));
        $query->expects($this->once())->method('setMaxResults')->with(1000)->will($this->returnValue($query));


        $response = $controller->searchDescriptionAction("search_string");
        $this->assertEquals($response->getContent(), '[]');
    }

    public function testSearchDescriptionAction_TwoExistOneAccessible()
    {
        $notAccessible = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $accessible = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projects = array($notAccessible, $accessible);

        $query = $this->getMockBuilder('MyQuery')
            ->disableOriginalConstructor()
            ->setMethods(array("getResult", "setMaxResults"))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("createQueryBuilder"))
            ->getMock();

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array("where", "getQuery", "setParameter"))
            ->getMock();


        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions', 'getOwnerAction'));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));


        $repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('p'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('where')->with($this->equalTo('p.description LIKE :token'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('setParameter')->with($this->equalTo('token'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
        $qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));
        $query->expects($this->once())->method('getResult')->will($this->returnValue($projects));
        $query->expects($this->once())->method('setMaxResults')->with(1000)->will($this->returnValue($query));;

        $notAccessible->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->at(0))->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false}'));

        $accessible->expects($this->exactly(3))->method('getId')->will($this->returnValue(2));
        $controller->expects($this->at(1))->method('checkReadProjectPermissions')->with($this->equalTo(2))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getOwnerAction')->with($this->equalTo(2))->will($this->returnValue(new Response('{"success":true,"response":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}')));

        $accessible->expects($this->once())->method('getName')->will($this->returnValue('Name'));
        $accessible->expects($this->once())->method('getDescription')->will($this->returnValue('Description'));

        $response = $controller->searchDescriptionAction("search_string");
        $this->assertEquals($response->getContent(), '{"2":{"name":"Name","description":"Description","owner":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}}');
    }

    //---checkExistsAction
    public function testCheckExistsAction_Exists()
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->exactly(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->checkExistsAction(1);
        $this->assertEquals($response->getContent(), json_encode(array("success" => true)));

    }

    public function testCheckExistsAction_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->checkExistsAction(1);
        $this->assertEquals($response->getContent(), json_encode(array("success" => false)));

    }

    //---getProjectById
    public function testGetProjectById_Exists()
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->getProjectById(1);
        $this->assertEquals($response, $this->project);


    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetProjectById_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($repo));

        $controller->getProjectById(1);

    }

    //---checkWriteProjectPermissionsAction
    public function testCheckWriteProjectPermissionsAction_Yes()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Write permissions granted."}'));
        $response = $controller->checkWriteProjectPermissionsAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"Write permissions granted."}');

    }

    public function testCheckWriteProjectPermissionsAction_No()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkWriteProjectPermissions'));
        $controller->expects($this->once())->method('checkWriteProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":1}'));
        $response = $controller->checkWriteProjectPermissionsAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":1}');

    }

    //---checkReadProjectPermissionsAction
    public function testCheckReadProjectPermissionsAction_Yes()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"message":"Read permissions granted."}'));
        $response = $controller->checkReadProjectPermissionsAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"message":"Read permissions granted."}');

    }

    public function testCheckReadProjectPermissionsAction_No()
    {
        $controller = $this->setUpController($em, $fc, $security, array('checkReadProjectPermissions'));
        $controller->expects($this->once())->method('checkReadProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"message":"Read permissions not granted.","error":"You have no read permissions for this project.","id":1}'));
        $response = $controller->checkReadProjectPermissionsAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"message":"Read permissions not granted.","error":"You have no read permissions for this project.","id":1}');

    }
    //---currentPrivateProjectRecordsAction
    public function testCurrentPrivateProjectRecordsAction_NotLoggedIn()
    {
        $controller = $this->setUpController($em, $fc, $security, NULL);

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = "anon.";

        $security->expects($this->once())->method("getToken")->will($this->returnValue($token));

        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $response = $controller->currentPrivateProjectRecordsAction();

        $this->assertEquals($response->getContent(), '{"success":false,"message":"User not logged in"}');

    }

    public function testCurrentPrivateProjectRecordsAction_Success()
    {
        $controller = $this->setUpController($em, $fc, $security, NULL);

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();


        $expired = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $never_expires = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $not_started = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->once())->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->once())->method('getExpires')->will($this->returnValue(new \DateTime('3102-01-01')));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(2));
        $valid->expects($this->once())->method('getDescription')->will($this->returnValue("valid"));

        $never_expires->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $never_expires->expects($this->once())->method('getExpires')->will($this->returnValue(null));
        $never_expires->expects($this->once())->method('getNumber')->will($this->returnValue(3));
        $never_expires->expects($this->once())->method('getDescription')->will($this->returnValue("never_expires"));

        $not_started->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('3010-01-01')));


        $prv = array($expired, $valid, $never_expires, $not_started);

        $security->expects($this->once())->method("getToken")->will($this->returnValue($token));

        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($repo));

        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $repo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue($prv));

        $response = $controller->currentPrivateProjectRecordsAction();

        $this->assertEquals($response->getContent(), '{"success":true,"message":"User records retrieved successfully.","list":[{"description":"valid","expires":"3102-01-01","number":2},{"description":"never_expires","expires":null,"number":3}]}');

    }

    //---canCreatePrivateProjectAction
    public function testCanCreatePrivateProjectAction_Yes()
    {
        $controller = $this->setUpController($em, $fc, $security, array('canCreatePrivateProject'));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true,"availiable":1}'));
        $response = $controller->canCreatePrivateProjectAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"availiable":1}');
    }

    //---canCreatePrivateProjectAction
    public function testCanCreatePrivateProjectAction_No()
    {
        $controller = $this->setUpController($em, $fc, $security, array('canCreatePrivateProject'));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project."}'));
        $response = $controller->canCreatePrivateProjectAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project."}'
        );
    }

    //---canCreatePrivateProject
	public function testCanCreatePrivateProject_YesFromValid()
	{
        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prvRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();


        $prv = array($expired, $valid);

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($projRepo));
        $projRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($private,$public)));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $private->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($prvRepo));
        $prvRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue($prv));

        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('3102-01-01')));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(2));

        $response = $controller->call_canCreatePrivateProject(1);
        $this->assertEquals($response, '{"success":true,"available":1}');
	}

    public function testCanCreatePrivateProject_YesFromValidThatNeverExpires()
    {
        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prvRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();


        $prv = array($expired, $valid);

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($projRepo));
        $projRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($private,$public)));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $private->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($prvRepo));
        $prvRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue($prv));

        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->once())->method('getExpires')->will($this->returnValue(NULL));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(2));

        $response = $controller->call_canCreatePrivateProject(1);
        $this->assertEquals($response, '{"success":true,"available":1}');
    }

    public function testCanCreatePrivateProject_NoHaveNoPrivate()
    {


        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prvRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();


        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($projRepo));
        $projRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($public)));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));


        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($prvRepo));
        $prvRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array()));

        $response = $controller->call_canCreatePrivateProject(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot create private project."}');
    }

    public function testCanCreatePrivateProject_NoExpired()
    {



        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prvRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($projRepo));
        $projRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($public)));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));


        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($prvRepo));
        $prvRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($expired)));

        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $response = $controller->call_canCreatePrivateProject(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot create private project."}'
        );
    }

    public function testCanCreatePrivateProject_AlreadyHasMany()
    {
        $private = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $public = $this->getMockBuilder('Codebender\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();

        $projRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $prvRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findByOwner'))
            ->getMock();

        $expired = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();

        $valid = $this->getMockBuilder('Codebender\ProjectBundle\Entity\PrivateProjects')
            ->disableOriginalConstructor()
            ->getMock();


        $prv = array($expired, $valid);

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);

        $em->expects($this->at(0))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:Project'))->will($this->returnValue($projRepo));
        $projRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue(array($private,$public)));
        $public->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $private->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));

        $em->expects($this->at(1))->method('getRepository')->with($this->equalTo('CodebenderProjectBundle:PrivateProjects'))->will($this->returnValue($prvRepo));
        $prvRepo->expects($this->once())->method('findByOwner')->with($this->equalTo(1))->will($this->returnValue($prv));

        $expired->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $expired->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('2011-01-01')));

        $valid->expects($this->once())->method('getStarts')->will($this->returnValue(new \DateTime('2010-01-01')));
        $valid->expects($this->exactly(2))->method('getExpires')->will($this->returnValue(new \DateTime('3102-01-01')));
        $valid->expects($this->once())->method('getNumber')->will($this->returnValue(1));

        $response = $controller->call_canCreatePrivateProject(1);
        $this->assertEquals($response, '{"success":false,"error":"Cannot create private project."}');
    }


    //---canCreateFile
    public function testCanCreateFile()
    {
        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);
        $response = $controller->call_canCreateFile(1,"filename");
        $this->assertEquals($response,'{"success":true}');

    }

    //---nameIsValid
    public function testNameIsValid_Yes()
    {
        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);
        $response = $controller->call_nameIsValid("Valid Project Name");
        $this->assertEquals($response,'{"success":true}');

    }
    public function testNameIsValid_No()
    {
        $controller = $this->setUpPrivateTesterController($em, $fc, $security, NULL);
        $response = $controller->call_nameIsValid("Invalid/ Project/ Name");
        $this->assertEquals($response,'{"success":false,"error":"Invalid Project Name. Please enter a new one."}');

    }

    //---checkReadProjectPermissions
    public function testcheckReadProjectPermissions_Public()
    {
        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(true));
        $response = $controller->call_checkReadProjectPermissions(1);
        $this->assertEquals($response, '{"success":true,"message":"Read permissions granted."}');

    }

    public function testcheckReadProjectPermissions_Yes()
    {

        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $security = new SecurityContext($authManager, $decisionManager);


        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();


        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\ProjectControllerPrivateTester', $methods = array('getProjectById'), $arguments = array($em, $fc, $security));


        $currentUser = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $security->setToken($token);

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $token->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));
        $decisionManager->expects($this->once())->method('decide')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $currentUser->expects($this->once())->method('getID')->will($this->returnValue(1));
        $response = $controller->call_checkReadProjectPermissions(1);
        $this->assertEquals($response, '{"success":true,"message":"Read permissions granted."}');

    }

    public function testcheckReadProjectPermissions_No()
    {

        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $security = new SecurityContext($authManager, $decisionManager);


        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();


        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\ProjectControllerPrivateTester', $methods = array('getProjectById'), $arguments = array($em, $fc, $security));



        $currentUser = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $security->setToken($token);

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $token->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));
        $decisionManager->expects($this->once())->method('decide')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $currentUser->expects($this->once())->method('getID')->will($this->returnValue(2));
        $response = $controller->call_checkReadProjectPermissions(1);
        $this->assertEquals($response, '{"success":false,"message":"Read permissions not granted.","error":"You have no read permissions for this project.","id":1}');

    }

    public function testcheckReadProjectPermissions_NotLoggedIn()
    {

        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $security = new SecurityContext($authManager, $decisionManager);


        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();


        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\ProjectControllerPrivateTester', $methods = array('getProjectById'), $arguments = array($em, $fc, $security));

        $currentUser = "anon.";

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $security->setToken($token);

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $token->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));
        $decisionManager->expects($this->once())->method('decide')->will($this->returnValue(false));
        $this->project->expects($this->once())->method('getIsPublic')->will($this->returnValue(false));
        $response = $controller->call_checkReadProjectPermissions(1);
        $this->assertEquals($response, '{"success":false,"message":"Read permissions not granted.","error":"You have no read permissions for this project.","id":1}');


    }

    public function testcheckReadProjectPermissions_Admin()
    {

        $authManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');
        $decisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');

        $security = new SecurityContext($authManager, $decisionManager);


        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();


        $fc = $this->getMockBuilder('Codebender\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\ProjectControllerPrivateTester', $methods = array('getProjectById'), $arguments = array($em, $fc, $security));


        $currentUser = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $security->setToken($token);

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $token->expects($this->once())->method('isAuthenticated')->will($this->returnValue(true));
        $decisionManager->expects($this->once())->method('decide')->will($this->returnValue(true));
        $response = $controller->call_checkReadProjectPermissions(1);
        $this->assertEquals($response, '{"success":true,"message":"Read permissions granted."}');

    }

    public function testcheckWriteProjectPermissions_Yes()
    {
        $currentUser = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $currentUser->expects($this->once())->method('getID')->will($this->returnValue(1));
        $response = $controller->call_checkWriteProjectPermissions(1);
        $this->assertEquals($response, '{"success":true,"message":"Write permissions granted."}');

    }

    public function testcheckWriteProjectPermissions_No()
    {

        $currentUser = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getID')->will($this->returnValue(1));
        $currentUser->expects($this->once())->method('getID')->will($this->returnValue(2));
        $response = $controller->call_checkWriteProjectPermissions(1);
        $this->assertEquals($response, '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":1}');

    }

    public function testcheckWriteProjectPermissions_NotLoggedIn()
    {

        $currentUser = "anon.";

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($currentUser));

        $response = $controller->call_checkWriteProjectPermissions(1);
        $this->assertEquals($response, '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":1}');

    }

    //---nameExists
    public function testNameExists_Yes()
    {
        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('listAction'));
        $controller->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response('[{"id":1,"name":"name 1","description":"desc","is_public":true},{"id":2,"name":"name 2","description":"des","is_public":false}]')));

        $response = $controller->call_nameExists(1, "name 1");
        $this->assertEquals($response,'{"success":true}');

    }

    public function testNameExists_No()
    {
        $controller = $this->setUpPrivateTesterController($em, $fc, $security, array('listAction'));
        $controller->expects($this->once())->method('listAction')->with($this->equalTo(1))->will($this->returnValue(new Response('[{"id":1,"name":"name 1","description":"desc","is_public":true},{"id":2,"name":"name 2","description":"des","is_public":false}]')));

        $response = $controller->call_nameExists(1, "name 3");
        $this->assertEquals($response,'{"success":false}');

    }

	//useful functions
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

        $controller = $this->getMock('Codebender\ProjectBundle\Controller\ProjectController', $methods = $m, $arguments = array($em, $fc, $security));
        return $controller;
    }
    private function setUpPrivateTesterController(&$em, &$fc, &$security, $m)
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

        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\ProjectControllerPrivateTester', $methods = $m, $arguments = array($em, $fc, $security));
        return $controller;
    }

}
