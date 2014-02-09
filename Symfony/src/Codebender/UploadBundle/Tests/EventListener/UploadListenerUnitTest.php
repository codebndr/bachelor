<?php

namespace Codebender\UploadBundle\Tests\ErrorHandler;


class UploadListenerUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_noType()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();


        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue(NULL));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $listener->onValidate($event);


    }

    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_noUser()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $user = "anon.";

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('project'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $logController->expects($this->once())->method('logAction')->with($this->equalTo(null), $this->equalTo(4), $this->equalTo('UPLOAD_PROJECT'), $this->equalTo('{"success":false,"error":"Not logged in"}'));

        $listener->onValidate($event);


    }

    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_ProjectUnsupportedMime()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('project'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/html'));
        $file->expects($this->at(1))->method('getMimeType')->will($this->returnValue('text/html'));
        $file->expects($this->at(2))->method('getExtension')->will($this->returnValue('html'));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(4), $this->equalTo('UPLOAD_PROJECT'), $this->equalTo('{"success":false,"error":"Validation Error on file","mimeType":"text\/html","extension":"html"}'));

        $listener->onValidate($event);

    }

    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_ProjectUnsupportedExt()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('project'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(1))->method('getExtension')->will($this->returnValue('html'));
        $file->expects($this->at(2))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(3))->method('getExtension')->will($this->returnValue('html'));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(4), $this->equalTo('UPLOAD_PROJECT'), $this->equalTo('{"success":false,"error":"Validation Error on file","mimeType":"text\/plain","extension":"html"}'));

        $listener->onValidate($event);

    }

    public function testOnValidate_ProjectSuccess()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('project'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(1))->method('getExtension')->will($this->returnValue('ino'));

        $listener->onValidate($event);

    }


    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_FileUnsupportedMime()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('file'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/html'));
        $file->expects($this->at(1))->method('getMimeType')->will($this->returnValue('text/html'));
        $file->expects($this->at(2))->method('getExtension')->will($this->returnValue('html'));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(29), $this->equalTo('UPLOAD_FILE'), $this->equalTo('{"success":false,"error":"Validation Error on file","mimeType":"text\/html","extension":"html"}'));

        $listener->onValidate($event);

    }

    /**
     * @expectedException Oneup\UploaderBundle\Uploader\Exception\ValidationException
     */

    public function testOnValidate_FileUnsupportedExt()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));


        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('file'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(1))->method('getExtension')->will($this->returnValue('html'));
        $file->expects($this->at(2))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(3))->method('getExtension')->will($this->returnValue('html'));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1), $this->equalTo(29), $this->equalTo('UPLOAD_FILE'), $this->equalTo('{"success":false,"error":"Validation Error on file","mimeType":"text\/plain","extension":"html"}'));

        $listener->onValidate($event);

    }

    public function testOnValidate_FileSuccess()
    {
        $event = $this->getMockBuilder('Oneup\UploaderBundle\Event\ValidationEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getFile', 'getRequest'))
            ->getMock();

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder('\Oneup\UploaderBundle\Uploader\File\FileInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getMimeType', 'getExtension'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();

        $req = $this->getMock("Symfony\Component\HttpFoundation\ParameterBag", array("get"));

        $request->request = $req;

        $listener = $this->setUpListener($logController, $security, $projectController, NULL);

        $event->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $req->expects($this->once())->method('get')->with($this->equalTo('uploadType'))->will($this->returnValue('file'));

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $event->expects($this->once())->method('getFile')->will($this->returnValue($file));
        $file->expects($this->at(0))->method('getMimeType')->will($this->returnValue('text/plain'));
        $file->expects($this->at(1))->method('getExtension')->will($this->returnValue('c'));

        $listener->onValidate($event);

    }


    private function setUpListener(&$logController, &$security, &$projectController, $m)
    {
        $this->initArguments($logController, $security, $projectController);
        $listener = $this->getMock('Codebender\UploadBundle\EventListener\UploadListener', $m, array($logController, $security, $projectController));
        return $listener;
    }

    private function initArguments(&$logController, &$security, &$projectController)
    {
        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $projectController = $this->getMockBuilder("Codebender\ProjectBundle\Controller\SketchController")
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();


    }
}
