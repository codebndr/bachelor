<?php

namespace Codebender\StaticBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerFunctionalTest extends WebTestCase
{

	public function testWalkthroughAction_ValidPages()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/walkthrough/page/1');
		$this->assertEquals(1, $crawler->filter('html:contains("Feel the magic!")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Page 1 of")')->count());

		//TODO: Make this more interactive. i.e. pressing the button instead of moving to the next page
		$crawler = $client->request('GET', '/static/walkthrough/page/2');
		$this->assertEquals(1, $crawler->filter('html:contains("Page 2 of")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Please wait")')->count());

		$crawler = $client->request('GET', '/static/walkthrough/page/3');
		$this->assertEquals(1, $crawler->filter('html:contains("Page 3 of")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Driver Installation")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("We strongly encourage you to install")')->count());

		$crawler = $client->request('GET', '/static/walkthrough/page/4');
		$this->assertEquals(1, $crawler->filter('html:contains("Page 4 of")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Start")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("You should now have all the necessary")')->count());

		$crawler = $client->request('GET', '/static/walkthrough/page/5');
		$this->assertEquals(1, $crawler->filter('html:contains("Page 5 of")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Congratulations!")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Congratulations, you just completed the")')->count());

        $crawler = $client->request('GET', '/static/walkthrough/page/download-complete');
        $this->assertEquals(1, $crawler->filter('html:contains("Warning")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Sorry, you\'re not done yet.")')->count());
	}

	public function testWalkthroughAction_LandingPageLoggedIn()
	{
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'tester',
			'PHP_AUTH_PW' => 'testerPASS',
		));

		$crawler = $client->request('GET', '/static/walkthrough/page/5');
		$this->assertEquals(1, $crawler->filter('html:contains("Page 5 of")')->count());
		$this->assertEquals(0, $crawler->filter('input[value=Register]')->count());
	}

	public function testWalkthroughAction_LandingPageAnonymous()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/walkthrough/page/5');
		$this->assertEquals(1, $crawler->filter('html:contains("It\'s quite simple really.")')->count());
	}

	public function testWalkthroughAction_Invalid()
	{
		$client = static::createClient();

		$client->request('GET', '/static/walkthrough/page/0');
		$this->assertTrue($client->getResponse()->isRedirect());

		$client->request('GET', '/static/walkthrough/page/6');
		$this->assertTrue($client->getResponse()->isRedirect());
	}

	public function testPluginAction()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/static/plugin');

		$this->assertEquals(1, $crawler->filter('html:contains("Firefox")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Google Chrome")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Chromium")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("The Plugin")')->count());
		$this->assertEquals(1, $crawler->filter('html:contains("Downloading the correct plugin for your browser or OS!")')->count());
		$this->assertGreaterThanOrEqual(1, $crawler->filter('h1')->count());
		$this->assertGreaterThanOrEqual(2, $crawler->filter('h3')->count());
	}

    public function testUploadBootloaderAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/static/upload/bootloader');

        $this->assertEquals(1, $crawler->filter('html:contains("Upload Bootloader")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Please select your programmer, device and usb port from the lists below and click start the begin the upload.")')->count());
    }

}
