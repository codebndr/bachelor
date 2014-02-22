<?php

namespace Codebender\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class OptionsControllerFunctionalTest extends WebTestCase
{
    public function testOptionsEditAction_notLoggedIn()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users/options');
        $client->followRedirects();
        $this->assertEquals(0, $crawler->filter('h2:contains("User Account Options")')->count());

    }

    public function testOptionsEditAction_formRendered()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $crawler = $client->request('GET', '/users/options');
        $this->assertEquals(1, $crawler->filter('h2:contains("User Account Options")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("optionTester")')->count());
        $this->assertEquals("Type your current password", $crawler->filter('input')->eq(5)->attr('placeholder'));
        $this->assertEquals("Type your new password", $crawler->filter('input')->eq(6)->attr('placeholder'));
        $this->assertEquals("Type your new password", $crawler->filter('input')->eq(7)->attr('placeholder'));
    }

    public function testOptionsEditAction_changeNonSensitiveSuccess()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'Johnathan',
                'options[lastname]' => 'Dowe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebendercc',
                'options[currentPassword]' => 'optionTesterPASS',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}');
        $this->resetCredentials();
    }

    public function testOptionsEditAction_changeNonSensitive_FalseFirstname()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'Johnathan##',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => '',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"firstname":["Sorry, your Firstname can only contain a letters and \' - _"],"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again."}');
        $this->resetCredentials();
    }


    public function testOptionsEditAction_changeNonSensitive_InvalidEmail()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'testercodebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => '',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"email":["Sorry, this is not a valid Email address","Please provide your Current Password to change your Email"],"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again."}');
    }

    public function testOptionsEditAction_changeEmail_ValidEmail_NoPassword()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester1@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => '',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"email":["Please provide your Current Password to change your Email"],"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again."}');
    }

    public function testOptionsEditAction_changeEmail_ValidEmail_WrongPassword()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester1@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'WrongPASS',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"email":["Please provide your Current Password to change your Email"],"currentPassword":["Sorry, wrong password!"],"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again."}'
        );
    }

    public function testOptionsEditAction_changeEmail_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester1@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'optionTesterPASS',
                'options[plainPassword][new]' => '',
                'options[plainPassword][confirm]' => '',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}'
        );
        $this->resetCredentials();
    }


    public function testOptionsEditAction_changePassword_success()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'optionTesterPASS',
                'options[plainPassword][new]' => 'newtesterPASS',
                'options[plainPassword][confirm]' => 'newtesterPASS',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}'
        );
        $this->resetCredentials('newtesterPASS', "optionTesterPASS");
    }

    public function testOptionsEditAction_changePassword_doNotMatch()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'optionTesterPASS',
                'options[plainPassword][new]' => 'newtesterPASS1',
                'options[plainPassword][confirm]' => 'newtesterPASS2',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again.","plainPassword_confirm":{"new":["The New Password fields must match."]}}'
        );
    }

    public function testOptionsEditAction_changePassword_invalidPass()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'optionTesterPASS',
                'options[plainPassword][new]' => 'invalidpass',
                'options[plainPassword][confirm]' => 'invalidpass',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again.","plainPassword_confirm":["Your Password is too simple, try mix and matching Letters, Numbers or Symbols, to make it more secure."]}'
        );
    }

    public function testOptionsEditAction_changePassword_wrongOldPass()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => 'wrongPASS',
                'options[plainPassword][new]' => 'invalidpass',
                'options[plainPassword][confirm]' => 'invalidpass',
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

        $this->assertEquals($client->getResponse()->getContent(), '{"currentPassword":["Sorry, wrong password!"],"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again.","plainPassword_confirm":["Please provide your Current Password along with your New one."]}'
        );
    }

	public function testIscurrentPassword_yes()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $client->request('POST', '/users/iscurrentpassword', array('currentPassword' => 'optionTesterPASS'));
        $this->assertEquals($client->getResponse()->getContent(), '{"valid":true}');
    }

    public function testIscurrentPassword_no()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $client->request('POST', '/users/iscurrentpassword', array('currentPassword' => 'wrongPASS'));
        $this->assertEquals($client->getResponse()->getContent(), '{"valid":false}');
    }

    public function testIsEmailAvailableAction_yes()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $client->request('POST', '/users/isemailavailable', array('email' => 'available@email.com'));
        $this->assertEquals($client->getResponse()->getContent(), '{"valid":"available"}');
    }

    public function testIsEmailAvailableAction_Own()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $client->request('POST', '/users/isemailavailable', array('email' => 'optiontester@codebender.cc'));
        $this->assertEquals($client->getResponse()->getContent(), '{"valid":"own"}');
    }

    public function testIsEmailAvailableAction_inUse()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => 'optionTesterPASS',
        ));

        $client->request('POST', '/users/isemailavailable', array('email' => 'testacc@codebender.cc'));
        $this->assertEquals($client->getResponse()->getContent(), '{"valid":"inUse"}');
    }

    private function resetCredentials($pass = 'optionTesterPASS', $newPass = "")
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'optionTester',
            'PHP_AUTH_PW' => $pass,
        ));
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('options');

        $crawler = $client->request('GET', '/users/options');
        $form = $crawler->selectButton('Hidden submit')->form(
            array(
                'options[firstname]' => 'John',
                'options[lastname]' => 'Doe',
                'options[email]' => 'optiontester@codebender.cc',
                'options[twitter]' => 'codebender_cc',
                'options[currentPassword]' => $pass,
                'options[plainPassword][new]' => $newPass,
                'options[plainPassword][confirm]' => $newPass,
                'options[_token]' => $csrfToken
            ));

        $client->submit($form);

    }

}
