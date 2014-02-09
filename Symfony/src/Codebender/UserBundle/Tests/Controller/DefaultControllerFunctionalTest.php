<?php

namespace Codebender\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerFunctionalTest extends WebTestCase
{
    public function testExistsAction_UserExists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/exists/tester');
        $this->assertEquals(1, $crawler->filter('p:contains("true")')->count());
    }

    public function testExistsAction_UserInexistent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/exists/inexistent_user_123');
        $this->assertEquals(1, $crawler->filter('p:contains("false")')->count());
    }

    public function testGetUserAction_UserExists()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/getuser/tester');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"success":true\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
    }

    public function testGetUserAction_UserInexistent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/getuser/inexistent_user_123');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"success":false\')')->count());
    }

    public function testGetCurrentUserAction_UseLoggedIn()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'tester',
            'PHP_AUTH_PW' => 'testerPASS',
        ));
        $crawler = $client->request('GET', '/users/getcurrentuser');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"success":true\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
    }

    public function testGetCurrentUserAction_UserNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/getcurrentuser');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"success":false\')')->count());
    }

    public function testEnabledAction()
    {
        $client = static::createClient();
        $client->request('GET', '/users/enabled');
        $this->assertRegExp('/[0-9]+/',$client->getResponse()->getContent());
    }

    public function testActiveAction()
    {
        $client = static::createClient();
        $client->request('GET', '/users/active');
        $this->assertRegExp('/[0-9]+/',$client->getResponse()->getContent());
    }

    public function testSearchAction_onlyUsername()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/user/testacc');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"testacc"\')')->count());
    }

    public function testSearchAction_onlyName()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/user/myfirstname');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"firstname":"myfirstname"\')')->count());
    }

    public function testSearchAction_onlyTwitter()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/user/codebender_cc');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"firstname":"myfirstname"\')')->count());
    }

    public function testSearchAction_none()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/user/inexistent_user');
        $this->assertEquals('[]',$client->getResponse()->getContent());
    }

    public function testSearchNameAction_yes()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/name/myfirstname');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"firstname":"myfirstname"\')')->count());
    }

    public function testSearchNameAction_no()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/name/inexistent_user');
        $this->assertEquals('[]',$client->getResponse()->getContent());
    }

    public function testSearchUsernameAction_yes()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/username/tester');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"firstname":"myfirstname"\')')->count());
    }

    public function testSearchUsernameAction_no()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/username/inexistent_user');
        $this->assertEquals('[]',$client->getResponse()->getContent());
    }


    public function testSearchTwitterAction_yes()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/twitter/codebender_cc');
        $this->assertEquals(1, $crawler->filter('html:contains(\'"username":"tester"\')')->count());
        $this->assertEquals(1, $crawler->filter('html:contains(\'"firstname":"myfirstname"\')')->count());
    }

    public function testSearchTwitterAction_no()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/search/twitter/inexistent_user');
        $this->assertEquals('[]',$client->getResponse()->getContent());
    }



}
