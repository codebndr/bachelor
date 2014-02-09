<?php

namespace Codebender\ProjectBundle\Tests\Controller;

use Codebender\ProjectBundle\Controller\FilesController;

class FilesControllerTester extends FilesController
{
    public function createAction(){}

    public function deleteAction($id){}

    public function listFilesAction($id){}

    public function createFileAction($id, $filename, $code){}

    public function getFileAction($id, $filename){}

    public function setFileAction($id, $filename, $code){}

    public function deleteFileAction($id, $filename){}

    public function renameFileAction($id, $filename, $new_filename){}

    protected function listFiles($id){}

    public function call_fileExists($id, $filename)
    {
        return $this->fileExists($id, $filename);
    }

    public function call_canCreateFile($id, $filename)
    {
        return $this->canCreateFile($id,$filename);
    }

    public function call_nameIsValid($id, $name)
    {
        return $this->nameIsValid($id,$name);
    }
}
class FilesControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testFileExists_Yes()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");
        $controller = $this->setUpController(array('listFiles'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1))->will($this->returnValue($list));
        $response = $controller->call_fileExists(1, 'header.h');
        $this->assertEquals($response, '{"success":true,"message":"File exists."}');
    }
    public function testFileExists_No()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");
        $controller = $this->setUpController(array('listFiles'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1))->will($this->returnValue($list));
        $response = $controller->call_fileExists(1, 'header2.h');
        $this->assertEquals($response, '{"success":false,"message":"File does not exist.","filename":"header2.h","error":"File header2.h does not exist."}');
    }

    public function testCanCreateFile_Yes()
    {
        $controller = $this->setUpController(array('fileExists'));
        $controller->expects($this->once())->method('fileExists')->with($this->equalTo(1), $this->equalTo('header1.h'))->will($this->returnValue('{"success":false}'));
        $response = $controller->call_canCreateFile(1, 'header1.h');
        $this->assertEquals($response, '{"success":true,"message":"File can be created."}');
    }
    public function testCanCreateFile_No()
    {
        $controller = $this->setUpController(array('fileExists'));
        $controller->expects($this->once())->method('fileExists')->with($this->equalTo(1), $this->equalTo('header1.h'))->will($this->returnValue('{"success":true}'));
        $response = $controller->call_canCreateFile(1, 'header1.h');
        $this->assertEquals($response, '{"success":false,"message":"File cannot be created.","id":1,"filename":"header1.h","error":"This file already exists"}');
    }
    public function testNameIsValid_Yes()
    {
        $controller = $this->setUpController(NULL);
        $res = $controller->call_nameIsValid(1,'header.h');
        $this->assertEquals($res, '{"success":true}');
    }
    public function testNameIsValid_No()
    {
        $controller = $this->setUpController(NULL);
        $res = $controller->call_nameIsValid(1,'<invalid>');
        $this->assertEquals($res, '{"success":false,"error":"Invalid File Name. Please enter a new one."}');
    }
    private function setUpController($m)
    {
        $controller = $this->getMock('Codebender\ProjectBundle\Tests\Controller\FilesControllerTester', $methods = $m);
        return $controller;
    }
}
