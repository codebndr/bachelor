<?php

namespace Codebender\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerFunctionalTest extends WebTestCase
{
	public function testIndexAction_Anonymous() // Test homepage and redirection
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/');

		$this->assertEquals(1, $crawler->filter('html:contains("code fast. code easy. codebender")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("online development & collaboration ")')->count());
	}

	public function testIndexAction_PrivateProjectCreation() // Test homepage redirection for logged in users
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/');

		$this->assertEquals(1, $crawler->filter('span:contains("Project Type:")')->count());

		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'testacc',
			'PHP_AUTH_PW' => 'testaccPWD',
		));

		$crawler = $client->request('GET', '/');

		$this->assertEquals(0, $crawler->filter('span:contains("Project Type:")')->count());
	}

	public function testIndexAction_LoggedIn() // Test homepage redirection for logged in users
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/');

		$this->assertEquals(1, $crawler->filter('h2:contains("Hello tester!")')->count());
		$this->assertEquals(1, $crawler->filter('h4:contains("Create a new project:")')->count());
	}

	public function testUserAction_UserExists() // Test user page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$this->assertEquals(1, $crawler->filter('html:contains("tester")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("myfirstname")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("mylastname")')->count());

		$matcher = array('id'   => 'user_projects');
		$this->assertTag($matcher, $client->getResponse()->getContent());
	}

	public function testUserAction_UserUnknown() // Test user page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/unknown_user');

		$this->assertEquals(1, $crawler->filter('h3:contains("There is no such user.")')->count());
	}

	public function testUserActionLinksToSketchView_SketchViewWorks() // Test project page
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/user/tester');

		$client->followRedirects();
        $this->assertEquals(1, $crawler->filter('h1:contains("myfirstname mylastname")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Projects")')->count());

		$link = $crawler->selectLink("test_project")->link();
		$crawler = $client->click($link);
		$this->assertEquals(1, $crawler->filter('h1:contains("codebender project")')->count());
	}

	public function testProjectActionExists()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/sketch:1');

		$this->assertEquals(1, $crawler->filter('html:contains("a project used to test the search function")')->count());

		//TODO: Use selenium to make sure this works fine.
		$this->markTestIncomplete('Use selenium to make sure this works fine.');
	}

	public function testProjectActionNonExistent()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/sketch:9999999');

		$this->assertEquals(1, $crawler->filter('html:contains("There is no such project")')->count());
	}

	public function testProjectAction_embedded()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/embed/sketch:1');

		$this->assertEquals(1, $crawler->filter('html:contains("test_project.ino")')->count());

		//TODO: Use selenium to make sure this works fine.
		$this->markTestIncomplete('Use selenium to make sure this works fine.');
	}

	public function testProjectfilesAction_Success_test_project()
	{
		$client = static::createClient();

		// Directly submit a form
		$client->request('POST', '/files', array('project_id' => '1'));
		$this->assertEquals($client->getResponse()->getContent(), '{"test_project.ino":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}');
	}

	public function testProjectfilesAction_ProjectNotFound()
	{
		$client = static::createClient();

		// Directly submit a form
		$crawler = $client->request('POST', '/files', array('project_id' => '99999'));
		$this->assertEquals($client->getResponse()->getContent(), 'Project Not Found');
	}

	public function testLibraries()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/libraries');
		$this->assertEquals(1, $crawler->filter('html:contains("codebender libraries")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Request Library")')->count());

		$this->assertEquals(1, $crawler->filter('h2:contains("Examples")')->count());
		$this->assertEquals(1, $crawler->filter('h2:contains("Builtin Libraries")')->count());
		$this->assertEquals(1, $crawler->filter('h2:contains("External Libraries")')->count());

		$this->assertEquals(1, $crawler->filter('html:contains("01.Basics")')->count());
	}

	public function testExampleAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/example/01.Basics/Blink');
		$this->assertEquals(1, $crawler->filter('h1:contains("01.Basics : Blink")')->count());
	}

	public function testBoardsAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/boards');
		$this->assertEquals(1, $crawler->filter('html:contains("codebender boards")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Request Board")')->count());

		$this->assertEquals(1, $crawler->filter('h4:contains("Arduino Uno")')->count());
		$this->assertEquals(1, $crawler->filter('h4:contains("Arno")')->count());
	}

	public function testEmbeddedCompilerFlasherAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/embed/compilerflasher.js');
		$response = $client->getResponse();
		$this->assertEquals( $response->headers->get('content_type'), 'text/javascript; charset=UTF-8');
	}
}
