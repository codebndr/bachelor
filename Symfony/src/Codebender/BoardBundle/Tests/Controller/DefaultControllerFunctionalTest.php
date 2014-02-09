<?php

namespace Codebender\BoardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerFunctionalTest extends WebTestCase
{
    public function testListAction_NotLoggedIn()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/board/listboards');

        $this->assertEquals(1, $crawler->filter('html:contains("Arduino Uno")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("The Uno is the reference model for the Arduino platform")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Arduino Leonardo")')->count());

    }

    public function testListAction_LoggedInHasBoard()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));


        $crawler = $client->request('GET', '/board/listboards');

        $this->assertEquals(1, $crawler->filter('html:contains("Arduino Uno")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Arduino Leonardo")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Arduino Custom")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Tester\'s custom board")')->count());
    }
}
