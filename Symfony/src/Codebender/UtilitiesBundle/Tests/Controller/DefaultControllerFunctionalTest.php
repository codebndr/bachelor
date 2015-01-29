<?php

namespace Codebender\UtilitiesBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerFunctionalTest extends WebTestCase
{
    public function testNewprojectAction_notLoggedIn()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('POST', '/utilities/newproject');

        $this->assertEquals(1, $crawler->filter('html:contains("code fast. code easy. codebender")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("online development & collaboration ")')->count());
    }

    public function testNewprojectAction_LoggedIn_cloneExample()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $client->followRedirects();
        $crawler = $client->request('POST', '/utilities/newproject', array('project_name' => 'project_to_test_new_project', 'code' => json_encode(array(array('filename' => 'project_to_test_new_project.ino', 'code'=>''))) ));

        $uri = $client->getRequest()->getUri();
        $pos = strrpos($uri, ":");

        $this->assertEquals(1, $crawler->filter('span:contains("project_to_test_new_project")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Delete")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Save")')->count());
    }

    public function testDeleteprojectAction()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/utilities/deleteproject/6', array());
        $this->assertEquals(1, $crawler->filter('h2:contains("Hello tester!")')->count());
        $this->assertEquals(1, $crawler->filter('h4:contains("Create a new project:")')->count());
    }

    public function testListFilenames_successWithIno()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'utilities/listfilenames/4/1');
        $this->assertEquals(1, $crawler->filter('html:contains("sample.ino")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("header.h")')->count());
    }

    public function testListFilenames_successNoIno()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'utilities/listfilenames/4/0');
        $this->assertEquals(0, $crawler->filter('html:contains("sample.ino")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("header.h")')->count());
    }

    public function testRenderDescriptionAction_hasPermissions()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/utilities/getprojectdescription/1');
        $this->assertEquals(1, $crawler->filter('html:contains("a project used to test the search function")')->count());
    }

    public function testRenderDescriptionAction_hasNoPermissions()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/utilities/getprojectdescription/3');
        $this->assertEquals(1, $crawler->filter('html:contains("Project description not found.")')->count());
    }

    public function testSetDescriptionAction_WrongMethod()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $crawler = $client->request('GET', '/utilities/setprojectdescription/1');
        $this->assertEquals(1, $crawler->filter('html:contains("Method Not Allowed")')->count());

    }

    public function testSetDescriptionAction_HasPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/setprojectdescription/3', array('data' => 'private description'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true}');

    }

    public function testSetDescriptionAction_HasNoPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/setprojectdescription/1', array('data' => 'cannot be done'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":"1"}');

    }

    public function testSetNameAction_WrongMethod()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $crawler = $client->request('GET', '/utilities/setprojectname/1');
        $this->assertEquals(1, $crawler->filter('html:contains("Method Not Allowed")')->count());

    }

    public function testSetNameAction_HasPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/setprojectname/3', array('data' => 'private_test_project'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true}');

    }

    public function testSetNameAction_HasNoPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/setprojectname/1', array('data' => 'cannot be done'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":"1"}');

    }

    public function testChangePrivacyAction_WrongMethod()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $crawler = $client->request('GET', '/utilities/changeprivacy/1');
        $this->assertEquals(1, $crawler->filter('html:contains("Method Not Allowed")')->count());

    }

    public function testChangePrivacyAction_Success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/changeprivacy/3');
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true}');

        $client->request('POST', '/utilities/changeprivacy/3');
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true}');
    }

    public function testChangePrivacyAction_Failure()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/changeprivacy/4');
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"error":"Cannot create private project."}');
    }

    public function testRenameFileAction_failureSecondIno()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/renamefile/7', array('oldFilename' => 'header.h', 'newFilename' => 'header.ino'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"id":"7","filename":"header.ino","error":"Cannot create second .ino file in the same project"}');
    }

    public function testRenameFileAction_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/renamefile/7', array('oldFilename' => 'header.h', 'newFilename' => 'head.h'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"message":"File renamed successfully."}');
    }

    public function testRenameFileAction_noSuchFile()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/renamefile/7', array('oldFilename' => 'header1.h', 'newFilename' => 'header.h'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"File does not exist.","filename":"header1.h","error":"File header1.h does not exist."}');
    }

    public function testRenameFileAction_noPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/renamefile/7', array('oldFilename' => 'header.h', 'newFilename' => 'header.h'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":"7"}');
    }

    public function testDownloadAction_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('GET', '/utilities/download/5');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testDownloadExampleAction_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('GET', '/utilities/downloadexample/WebServer//get/Ethernet/WebServer');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testSaveCodeAction_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/savecode/4', array('data' => '{"header.h":"","sample.ino":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"message":"Saved successfully."}');
    }

    public function testSaveCodeAction_invalidFile()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/savecode/4', array('data' => '{"head3r.h":"","sample.ino":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Save failed.","id":"testacc\/152ebb54ce481c","filename":"head3r.h"}');
    }

    public function testSaveCodeAction_noData()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/savecode/4');
        $this->assertEquals($client->getResponse()->getContent(), 'No data.');
    }

    public function testSaveCodeAction_invalidData()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/savecode/4', array('data' => 'invalid'));
        $this->assertEquals($client->getResponse()->getContent(), 'Wrong data.');
    }

    public function testCloneAction_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/utilities/clone/5');
        $this->assertEquals(1, $crawler->filter('html:contains("Undo All")')->count());

    }

    public function testCloneAction_noPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', '/utilities/clone/3');
        $this->assertEquals(1, $crawler->filter('html:contains("Error: You have no read permissions for this project.")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Hello testacc!")')->count());

    }

    public function testCreateFileAction_Success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/createfile/5', array('data' => '{"filename":"newFile.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"message":"File created successfully."}');

    }


    public function testCreateFileAction_NoPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/createfile/5', array('data' => '{"filename":"newFile.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":"5"}');

    }

    public function testCreateFileAction_InvalidFilename()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/createfile/5', array('data' => '{"filename":"<invalid.h>"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"File cannot be created.","id":"tester\/152ebb5726a41e","filename":"<invalid.h>","error":"Invalid File Name. Please enter a new one."}');

    }

    public function testCreateFileAction_ExistingFile()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/createfile/5', array('data' => '{"filename":"existing.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"File cannot be created.","id":"tester\/152ebb5726a41e","filename":"existing.h","error":"This file already exists"}');

    }

    public function testDeleteFileAction_Success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/deletefile/5', array('data' => '{"filename":"toDelete.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"message":"File deleted successfully."}');

    }


    public function testDeleteFileAction_NoPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('POST', '/utilities/deletefile/5', array('data' => '{"filename":"existing.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Write permissions not granted.","error":"You have no write permissions for this project.","id":"5"}');

    }

    public function testDeleteFileAction_NoFile()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $client->request('POST', '/utilities/deletefile/5', array('data' => '{"filename":"inexistent.h"}'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"File does not exist.","filename":"inexistent.h","error":"File inexistent.h does not exist."}');

    }

    // Not used any more
//    public function testCompileAction_success_syntax()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('POST', 'utilities/compile/', $parameters = array(), $files = array(), $server = array(), $content = '{"files":[{"filename":"header.h","content":""},{"filename":"sample.ino","content":"#include <Ethernet.h>\n\nvoid setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}],"format":"syntax","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}', $changeHistory = true);
//        $this->assertEquals(1, $crawler->filter('html:contains("success")')->count());
//        $this->assertEquals(1, $crawler->filter('html:contains("true")')->count());
//    }

    public function testCompileAction_fail_hex()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', 'utilities/compile/', $parameters = array(), $files = array(), $server = array(), $content = '{"files":[{"filename":"header.h","content":""},{"filename":"sample.ino","content":"void setup(\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}],"format":"hex","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}', $changeHistory = true);
        $this->assertEquals(1, $crawler->filter('html:contains("success")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("false")')->count());
    }

    public function testCompileAction_success_binary()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', 'utilities/compile/', $parameters = array(), $files = array(), $server = array(), $content = '{"files":[{"filename":"header.h","content":""},{"filename":"sample.ino","content":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}],"format":"binary","version":"105","build":{"mcu":"atmega328p","f_cpu":"16000000L","core":"arduino","variant":"standard"}}', $changeHistory = true);
        $this->assertEquals(1, $crawler->filter('html:contains("success")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("true")')->count());
    }

    public function testCompileAction_NoData()
    {
        $client = static::createClient();
        $client->request('POST', 'utilities/compile/');
        $this->assertEquals($client->getResponse()->getContent(), 'No data.');
    }

    public function testCompileAction_WrongData()
    {
        $client = static::createClient();
        $client->request('POST', 'utilities/compile/', $parameters = array(), $files = array(), $server = array(), $content = 'wrong', $changeHistory = true);
        $this->assertEquals($client->getResponse()->getContent(), 'Wrong data.');
    }

    public function testFlashAction()
    {
        $client = static::createClient();
        $client->request('GET', '/utilities/flash');
        $this->assertEquals('OK', $client->getResponse()->getContent());
    }

    public function testDownloadHexAction()
    {
        $client = static::createClient();
        $client->request('POST', '/utilities/gethex', array('hex' => '{"hex":"0123456789ABCDEF"}'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAddBoardAction_ErrorUploading()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $_FILES['boards']['tmp_name'] = '../Resources/valid_arduino.txt';
        $_FILES['boards']['name'] = 'valid_arduino.txt';
        $_FILES['boards']['type'] = 'text/plain';
        $_FILES['boards']['error'] = 1;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');


        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Error: Upload failed with error code 1")')->count());
    }

    public function testAddBoardAction_ErrorContentType()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $_FILES['boards']['tmp_name'] = '../Resources/valid_arduino.txt';
        $_FILES['boards']['name'] = 'valid_arduino.txt';
        $_FILES['boards']['type'] = 'text/html';
        $_FILES['boards']['error'] = 0;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');


        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Error: File type should be .txt.")')->count());
    }

    public function testAddBoardAction_CannotAdd()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $_FILES['boards']['tmp_name'] = '../Resources/valid_arduino.txt';
        $_FILES['boards']['name'] = 'valid_arduino.txt';
        $_FILES['boards']['type'] = 'text/plain';
        $_FILES['boards']['error'] = 0;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');

        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Error: Cannot add personal board.")')->count());
    }

    public function testAddBoardAction_TooMany()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $_FILES['boards']['tmp_name'] = 'src/Codebender/UtilitiesBundle/Tests/Resources/too_many_boards.txt';
        $_FILES['boards']['name'] = 'too_many_boards.txt';
        $_FILES['boards']['type'] = 'text/plain';
        $_FILES['boards']['error'] = 0;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');


        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Error: You can add up to")')->count());
    }

    public function testAddBoardAction_InvalidBoard()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $_FILES['boards']['tmp_name'] = 'src/Codebender/UtilitiesBundle/Tests/Resources/invalid_arduino_no_build.txt';
        $_FILES['boards']['name'] = 'invalid_arduino_no_build.txt';
        $_FILES['boards']['type'] = 'text/plain';
        $_FILES['boards']['error'] = 0;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');


        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Error: File does not have the required structure.")')->count());
    }

    public function testAddBoardAction_ValidBoard()
    {

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $_FILES['boards']['tmp_name'] = 'src/Codebender/UtilitiesBundle/Tests/Resources/valid_arduino.txt';
        $_FILES['boards']['name'] = 'valid_arduino.txt';
        $_FILES['boards']['type'] = 'text/plain';
        $_FILES['boards']['error'] = 0;
        $client->followRedirects();
        $crawler = $client->request('POST', 'utilities/addboard');


        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("1 boards were successfully added.")')->count());
    }

    public function testDeleteBoard_noPermissions()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', 'utilities/deleteboard/33');
        $this->assertEquals(1, $crawler->filter('html:contains("You have no permissions to delete this board.")')->count());
    }

    public function testDeleteBoard_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', 'utilities/deleteboard/33');
        $this->assertEquals(1, $crawler->filter('html:contains("was successfully deleted")')->count());

        $this->assertEquals(1, $crawler->filter('html:contains("codebender Bachelor boards")')->count());

    }

    public function testDeleteBoard_Builtin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));
        $client->followRedirects();
        $crawler = $client->request('GET', 'utilities/deleteboard/1');
        $this->assertEquals(1, $crawler->filter('html:contains("You have no permissions to delete this board.")')->count());

    }

    public function testEditBoard_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $client->request('POST', 'utilities/setboarddescription', array('id' => 32, 'name' => 'Arduino Custom', 'desc' => 'Tester\'s custom board'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":true,"new_name":"Arduino Custom","new_desc":"Tester\'s custom board"}');

    }

    public function testEditBoard_builtIn()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $client->request('POST', 'utilities/setboarddescription', array('id' => 1, 'name' => 'Arduino Custom', 'desc' => 'Tester\'s custom board'));
        $this->assertEquals($client->getResponse()->getContent(), '{"success":false,"message":"Cannot edit board \'Arduino Uno\'."}');

    }

    public function testLogAction()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('GET', '/utilities/log/message');
        $this->assertEquals('OK', $client->getResponse()->getContent());
    }

    public function testLogDatabaseAction_valid()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('GET', '/utilities/logdb/16/%7B%22ip%22:%22null%22%7D?_=1390760857692');
        $this->assertEquals('OK', $client->getResponse()->getContent());
    }

    public function testLogDatabaseAction_invalid()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $client->request('GET', '/utilities/logdb/1/"{}"');
        $this->assertEquals('Invalid Action ID', $client->getResponse()->getContent());
    }

}
