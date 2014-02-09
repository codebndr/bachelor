<?php

namespace Codebender\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class RegistrationControllerFunctionalTest extends WebTestCase
{
    public function testRegisterAction_TakenUsername()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'tester',
                'fos_user_registration_form[email]' => 'anotherTesterEmail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("The username is already used")')->count());
    }

    public function testRegisterAction_SmallUsername()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'a',
                'fos_user_registration_form[email]' => 'anotherTesterEmail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Username needs to be at least 3 characters long.")')->count());
    }

    public function testRegisterAction_BigUsername()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'DTbmSp1gae3170JcwI0JgrprK0DnUtfD7ie6caSq5rn3L1W7PDCxPxgtM8jgN3TNSOeqWmEjsKaq7Jfxzka2ux9nRaSUoyA1FaZKnal1kPh4rtYGOVFYReltSc4lXmjoAXRQqmOoJelvIxGsUdyMZYyx5xHdUJLFILAs6JR5pzzDrq13cu59mGwfdmgKLqbkCvmSURkxa07w4Zide1ILVDCwbro1kWMZDpnQz4LLLBFUek0seGUppGq1s7OcQM2QggV5',
                'fos_user_registration_form[email]' => 'anotherTesterEmail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Username cannot be longer than 255 characters.")')->count());
    }

    public function testRegisterAction_TakenEmail()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'tester1',
                'fos_user_registration_form[email]' => 'tester@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("The email is already used")')->count());
    }

    public function testRegisterAction_InvalidCSRF()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'anotherTester',
                'fos_user_registration_form[email]' => 'anotherTesterEmail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => ''
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("The CSRF token is invalid. Please try to resubmit the form.")')->count());
    }

    public function testRegisterAction_InvalidEmail()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'anotherTester',
                'fos_user_registration_form[email]' => 'invalidemail',
                'fos_user_registration_form[plainPassword][first]' => 'validPASS',
                'fos_user_registration_form[plainPassword][second]' => 'validPASS',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("The email is not valid")')->count());
    }

    public function testRegisterAction_InvalidUsername()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => '<invalid>',
                'fos_user_registration_form[email]' => 'validemail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'validPass',
                'fos_user_registration_form[plainPassword][second]' => 'validPass',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Username contains invalid characters. Please try again with a different one.")')->count());
    }

    public function testRegisterAction_BigPassword()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'user',
                'fos_user_registration_form[email]' => 'anotherTesterEmail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'DTbmSp1gae3170JcwI0JgrprK0DnUtfD7ie6caSq5rn3L1W7PDCxPxgtM8jgN3TNSOeqWmEjsKaq7Jfxzka2ux9nRaSUoyA1FaZKnal1kPh4rtYGOVFYReltSc4lXmjoAXRQqmOoJelvIxGsUdyMZYyx5xHdUJLFILAs6JR5pzzDrq13cu59mGwfdmgKLqbkCvmSURkxa07w4Zide1ILVDCwbro1kWMZDpnQz4LLLBFUek0seGUppGq1s7OcQM2QggV5',
                'fos_user_registration_form[plainPassword][second]' => 'DTbmSp1gae3170JcwI0JgrprK0DnUtfD7ie6caSq5rn3L1W7PDCxPxgtM8jgN3TNSOeqWmEjsKaq7Jfxzka2ux9nRaSUoyA1FaZKnal1kPh4rtYGOVFYReltSc4lXmjoAXRQqmOoJelvIxGsUdyMZYyx5xHdUJLFILAs6JR5pzzDrq13cu59mGwfdmgKLqbkCvmSURkxa07w4Zide1ILVDCwbro1kWMZDpnQz4LLLBFUek0seGUppGq1s7OcQM2QggV5',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Password cannot be longer than 255 characters.")')->count());
    }

    public function testRegisterAction_InvalidPassword()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'anotherTester',
                'fos_user_registration_form[email]' => 'validemail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'invalidpass',
                'fos_user_registration_form[plainPassword][second]' => 'invalidpass',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Password is too simple, try mix and matching Letters, Numbers or Symbols, to make it more secure.")')->count());
    }

    public function testRegisterAction_SmallPassword()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'anotherTester',
                'fos_user_registration_form[email]' => 'validemail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'small',
                'fos_user_registration_form[plainPassword][second]' => 'small',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("Your Password needs to be at least 6 characters long.")')->count());
    }

    public function testRegisterAction_NonMatchingPasswords()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'anotherTester',
                'fos_user_registration_form[email]' => 'validemail@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'PassWord1',
                'fos_user_registration_form[plainPassword][second]' => 'PassWord2',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referrer_username]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));

        $crawler = $client->submit($form);

        $this->assertEquals(1, $crawler->filter('h4:contains("Sign Up")')->count());
        $this->assertEquals(1, $crawler->filter('li:contains("The entered passwords don\'t match")')->count());
    }


    public function testRegisterAction_success_confirmation()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'registrationTester',
                'fos_user_registration_form[email]' => 'registrationTester@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'PassWord!',
                'fos_user_registration_form[plainPassword][second]' => 'PassWord!',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[referral_code]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));


        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('p:contains("An email has been sent to registrationTester@codebender.cc. It contains an activation link you must click to activate your account.")')->count());
        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('registrationTester');

        $this->assertEquals($user->isEnabled(), 0);
        $token = $user->getConfirmationToken();

        $crawler = $client->request('GET', '/register/confirm/'.$token);
        $this->assertEquals(1, $crawler->filter('html:contains("Thanks for choosing codebender!")')->count());

        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('registrationTester');
        $this->assertEquals($user->getConfirmationToken(), NULL);
        $this->assertEquals($user->isEnabled(), 1);

    }

    public function testRegisterAction_successWithReferrer()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register?referrer=tester&referral_code=');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'referrerTester',
                'fos_user_registration_form[email]' => 'referrerTester@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'PassWord!',
                'fos_user_registration_form[plainPassword][second]' => 'PassWord!',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));


        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('p:contains("An email has been sent to referrerTester@codebender.cc. It contains an activation link you must click to activate your account.")')->count());
        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('referrerTester');

        $this->assertEquals($user->isEnabled(), 0);
        $token = $user->getConfirmationToken();

        $crawler = $client->request('GET', '/register/confirm/'.$token);
        $this->assertEquals(1, $crawler->filter('html:contains("Thanks for choosing codebender!")')->count());

        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('referrerTester');
        $this->assertEquals($user->getConfirmationToken(), NULL);
        $this->assertEquals($user->isEnabled(), 1);
        $this->assertEquals($user->getReferrer()->getId(), 1);

    }

    public function testRegisterAction_successWithReferral()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/register?referrer=&referral_code=SecretSauce');
        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('registration');

        $form = $crawler->selectButton('Register')->form(
            array(
                'fos_user_registration_form[username]' => 'referralTester',
                'fos_user_registration_form[email]' => 'referralTester@codebender.cc',
                'fos_user_registration_form[plainPassword][first]' => 'PassWord!',
                'fos_user_registration_form[plainPassword][second]' => 'PassWord!',
                'fos_user_registration_form[firstname]' => '',
                'fos_user_registration_form[lastname]' => '',
                'fos_user_registration_form[twitter]' => '',
                'fos_user_registration_form[_token]' => $csrfToken
            ));


        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('p:contains("An email has been sent to referralTester@codebender.cc. It contains an activation link you must click to activate your account.")')->count());
        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('referralTester');

        $this->assertEquals($user->isEnabled(), 0);
        $token = $user->getConfirmationToken();

        $crawler = $client->request('GET', '/register/confirm/'.$token);
        $this->assertEquals(1, $crawler->filter('html:contains("Thanks for choosing codebender!")')->count());

        $user = static::$kernel->getContainer()->get('doctrine')->getManager()->getRepository('CodebenderUserBundle:User')->findOneByUsername('referralTester');
        $this->assertEquals($user->getConfirmationToken(), NULL);
        $this->assertEquals($user->isEnabled(), 1);
        $this->assertEquals($user->getPoints(), 40);

    }


}
