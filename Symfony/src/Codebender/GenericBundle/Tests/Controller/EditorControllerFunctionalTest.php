<?php

namespace Codebender\GenericBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EditorControllerFunctionalTest extends WebTestCase
{
	public function testEditAction()
	{
        //Public project
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/sketch:1');

		$this->assertEquals(1, $crawler->filter('html:contains("Save")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("test_project.ino")')->count());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
			'PHP_AUTH_PW' => 'testaccPWD',
        ));
        $crawler = $client->request('GET', '/sketch:1');

        $this->assertEquals(1, $crawler->filter('h1:contains("codebender Bachelor project")')->count());


        //Private project

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $crawler = $client->request('GET', '/sketch:3');

        $this->assertEquals(1, $crawler->filter('html:contains("Save")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(".ino")')->count());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'testacc',
            'PHP_AUTH_PW' => 'testaccPWD',
        ));

        $crawler = $client->request('GET', '/sketch:3');

        $this->assertEquals(1, $crawler->filter('h3:contains("There is no such project!")')->count());


	}

    public function testIncomplete()
    {
        //TODO Use selenium
        $this->markTestIncomplete('Use selenium to make sure this works fine.');
    }



}
