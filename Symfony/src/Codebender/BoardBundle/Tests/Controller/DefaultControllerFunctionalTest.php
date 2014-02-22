<?php

namespace Codebender\BoardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerFunctionalTest extends WebTestCase
{
    public function testListAction_NotLoggedIn()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/board/listboards');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);

        $json = json_decode($response->getContent());
        $name_one = "Arduino Uno";
        $name_two = "Arduino Leonardo";

        $names = array();
        foreach ($json AS $board) {
            $names[] = $board->name;
        }
        $this->assertTrue(in_array($name_one, $names));
        $this->assertTrue(in_array($name_two, $names));
    }

    public function testListAction_LoggedInHasBoard()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));

        $crawler = $client->request('GET', '/board/listboards');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);

        $json = json_decode($response->getContent());
        $name_one = "Arduino Uno";
        $name_two = "Arduino Leonardo";
        $name_three = "Arduino Custom";
        $description = 'Tester\'s custom board';

        $names = array();
        $descriptions = array();
        foreach ($json AS $board) {
            $names[] = $board->name;
            $descriptions[] = $board->description;
        }

        $this->assertTrue(in_array($name_one, $names));
        $this->assertTrue(in_array($name_two, $names));
        $this->assertTrue(in_array($name_three, $names));
        $this->assertTrue(in_array($description, $descriptions));
    }
}
