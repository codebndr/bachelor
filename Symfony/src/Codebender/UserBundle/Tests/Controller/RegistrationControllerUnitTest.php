<?php

namespace Codebender\UserBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerUnitTest extends \PHPUnit_Framework_TestCase
{

    private  $first_code =
        "/*
    Blink
    Turns on an LED on for one second, then off for one second, repeatedly.

    This example code is in the public domain.
*/

void setup()
{
    // initialize the digital pin as an output.
    // Pin 13 has an LED connected on most Arduino boards:
    pinMode(13, OUTPUT);
}

void loop()
{
    digitalWrite(13, HIGH); // set the LED on
    delay(1000); // wait for a second
    digitalWrite(13, LOW); // set the LED off
    delay(1000); // wait for a second
}
";

    private $second_code =
"/*
    Prints an incremental number the serial monitor
*/
int number = 0;

void setup()
{
    Serial.begin(9600);
}

void loop()
{
    Serial.println(number++);
    delay(500);
}
";

	public function testRegisterAction_renderForm_noReferral()
	{
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('getEngine'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('createView', 'getData'))
            ->getMockForAbstractClass();

        $formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('process'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername'))
            ->getMock();

        $templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->setMethods(array('renderResponse'))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.registration.form'))->will($this->returnValue($form));
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));
        $container->expects($this->at(2))->method('getParameter')->with($this->equalTo('fos_user.registration.confirmation.enabled'))->will($this->returnValue(true));

        $formHandler->expects($this->once())->method('process')->with($this->equalTo(true))->will($this->returnValue(false));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));

        $user->expects($this->once())->method('getUsername')->will($this->returnValue('blah'));

        $container->expects($this->at(3))->method('get')->with($this->equalTo('templating'))->will($this->returnValue($templating));
        $controller->expects($this->once())->method('getEngine')->will($this->returnValue('twig'));

        $form->expects($this->once())->method('createView')->will($this->returnValue('form view'));

        $templating->expects($this->once())->method('renderResponse')->with($this->equalTo('FOSUserBundle:Registration:register.html.twig'), $this->equalTo(array('form' => 'form view')))->will($this->returnValue('view'));

        $this->assertEquals($controller->registerAction(), 'view');


    }

    public function testRegisterAction_renderForm_withReferral()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('getEngine'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('createView', 'getData'))
            ->getMockForAbstractClass();

        $formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'generateReferrals'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername'))
            ->getMock();

        $templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->setMethods(array('renderResponse'))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.registration.form'))->will($this->returnValue($form));
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));
        $container->expects($this->at(2))->method('getParameter')->with($this->equalTo('fos_user.registration.confirmation.enabled'))->will($this->returnValue(true));

        $formHandler->expects($this->once())->method('process')->with($this->equalTo(true))->will($this->returnValue(false));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));

        $user->expects($this->once())->method('getUsername')->will($this->returnValue(null));

        $formHandler->expects($this->once())->method('generateReferrals')->will($this->returnValue($form));

        $container->expects($this->at(3))->method('get')->with($this->equalTo('templating'))->will($this->returnValue($templating));
        $controller->expects($this->once())->method('getEngine')->will($this->returnValue('twig'));

        $form->expects($this->once())->method('createView')->will($this->returnValue('form view'));

        $templating->expects($this->once())->method('renderResponse')->with($this->equalTo('FOSUserBundle:Registration:register.html.twig'), $this->equalTo(array('form' => 'form view')))->will($this->returnValue('view'));

        $this->assertEquals($controller->registerAction(), 'view');


    }

    public function testRegisterAction_register_needsCofirmation()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('createView', 'getData'))
            ->getMockForAbstractClass();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'generateReferrals'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getEmail'))
            ->getMock();

        $session = $this->getMockBuilder("Symfony\Component\HttpFoundation\Session\Session")
            ->disableOriginalConstructor()
            ->setMethods(array('set'))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.registration.form'))->will($this->returnValue($form));
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));
        $container->expects($this->at(2))->method('getParameter')->with($this->equalTo('fos_user.registration.confirmation.enabled'))->will($this->returnValue(true));

        $formHandler->expects($this->once())->method('process')->with($this->equalTo(true))->will($this->returnValue(true));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(19),$this->equalTo('PREREGISTRATION'),$this->equalTo(""));
        $container->expects($this->at(4))->method('get')->with($this->equalTo('session'))->will($this->returnValue($session));
        $user->expects($this->once())->method('getEmail')->will($this->returnValue('user@email.com'));

        $session->expects($this->once())->method('set')->with($this->equalTo('fos_user_send_confirmation_email/email'), $this->equalTo('user@email.com'));

        $controller->expects($this->once())->method('setFlash')->with($this->equalTo('fos_user_success'), $this->equalTo('registration.flash.user_created'));

        $container->expects($this->at(5))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_check_email'))->will($this->returnValue('fake/url'));
        $this->assertEquals($controller->registerAction()->getStatusCode(), 302);

    }

    public function testRegisterAction_register_needsNoCofirmation()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('createView', 'getData'))
            ->getMockForAbstractClass();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'generateReferrals'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getEmail'))
            ->getMock();

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $controller->setContainer($container);

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.registration.form'))->will($this->returnValue($form));
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));
        $container->expects($this->at(2))->method('getParameter')->with($this->equalTo('fos_user.registration.confirmation.enabled'))->will($this->returnValue(false));

        $formHandler->expects($this->once())->method('process')->with($this->equalTo(false))->will($this->returnValue(true));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $form->expects($this->once())->method('getData')->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(19),$this->equalTo('PREREGISTRATION'),$this->equalTo(""));

        $controller->expects($this->once())->method('setFlash')->with($this->equalTo('fos_user_success'), $this->equalTo('registration.flash.user_created'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('fake/url'));
        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));


        $this->assertEquals($controller->registerAction()->getStatusCode(), 302);

    }
    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */

    public function testConfirmAction_fail()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserMaager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);


        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue(null));

        $controller->confirmAction('token');
    }


    public function testConfirmAction_noReferrals()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('setConfirmationToken', 'getId', 'getUsernameCanonical', 'setEnabled', 'setLastLogin', 'setRegistrationDate'))
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserMaager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder('Codebender\UserBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('getUserAction'))
            ->getMock();

        $sketchController = $this->getMockBuilder('Codebender\ProjectBundle\Controller\SketchController')
            ->disableOriginalConstructor()
            ->setMethods(array('createprojectAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder('Codebender\BoardBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('createBoardsPlanAction'))
            ->getMock();

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue($user));
        $user->expects($this->once())->method('setConfirmationToken')->with($this->equalTo(null));
        $user->expects($this->once())->method('setEnabled')->with($this->equalTo(true));
        $user->expects($this->once())->method('setLastLogin');
        $user->expects($this->once())->method('setRegistrationDate');
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('updateUser')->with($this->equalTo($user));
        $container->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('redirectUrl'));

        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(20),$this->equalTo('REGISTRATION'),$this->equalTo(""));
        $user->expects($this->once())->method('getUsernameCanonical')->will($this->returnValue('username'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('getUserAction')->with($this->equalTo('username'))->will($this->returnValue(new Response('{"success":true, "id":1, "referrer_username":null, "referral_code":null}')));
        $container->expects($this->at(5))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(0))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Blink Example"), $this->equalTo($this->first_code))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(6))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(1))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Serial Comm Example"), $this->equalTo($this->second_code))->will($this->returnValue(new Response('{"success":true}')));

        $container->expects($this->at(7))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('createBoardsPlanAction')->with($this->equalTo(1), $this->equalTo('Signup Gift'));

        $response = $controller->confirmAction('token');
        $this->assertEquals($response->getTargetUrl(), 'redirectUrl');
    }


    public function testConfirmAction_withReferrer()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('setConfirmationToken', 'getId', 'getUsernameCanonical', 'setEnabled', 'setLastLogin', 'setRegistrationDate'))
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder('Codebender\UserBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('getUserAction', 'setReferrerAction', 'setKarmaAction', 'setPointsAction'))
            ->getMock();

        $sketchController = $this->getMockBuilder('Codebender\ProjectBundle\Controller\SketchController')
            ->disableOriginalConstructor()
            ->setMethods(array('createprojectAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder('Codebender\BoardBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('createBoardsPlanAction'))
            ->getMock();

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue($user));
        $user->expects($this->once())->method('setConfirmationToken')->with($this->equalTo(null));
        $user->expects($this->once())->method('setEnabled')->with($this->equalTo(true));
        $user->expects($this->once())->method('setLastLogin');
        $user->expects($this->once())->method('setRegistrationDate');
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('updateUser')->with($this->equalTo($user));
        $container->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('redirectUrl'));

        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(20),$this->equalTo('REGISTRATION'),$this->equalTo(""));
        $user->expects($this->once())->method('getUsernameCanonical')->will($this->returnValue('username'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getUserAction')->with($this->equalTo('username'))->will($this->returnValue(new Response('{"success":true, "id":1, "referrer_username":"referrer", "referral_code":null}')));
        $container->expects($this->at(5))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(0))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Blink Example"), $this->equalTo($this->first_code))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(6))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(1))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Serial Comm Example"), $this->equalTo($this->second_code))->will($this->returnValue(new Response('{"success":true}')));

        $container->expects($this->at(7))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('createBoardsPlanAction')->with($this->equalTo(1), $this->equalTo('Signup Gift'));

        $container->expects($this->at(8))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(1))->method('getUserAction')->with($this->equalTo('referrer'))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(9))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('setReferrerAction')->with($this->equalTo('username'), $this->equalTo('referrer'))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(10))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('setKarmaAction')->with($this->equalTo('username'), $this->equalTo(50))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(11))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('setPointsAction')->with($this->equalTo('username'), $this->equalTo(50))->will($this->returnValue(new Response('{"success":true}')));
        $response = $controller->confirmAction('token');
        $this->assertEquals($response->getTargetUrl(), 'redirectUrl');
    }

    public function testConfirmAction_invalidReferrer()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('setConfirmationToken', 'getId', 'getUsernameCanonical', 'setEnabled', 'setLastLogin', 'setRegistrationDate'))
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder('Codebender\UserBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('getUserAction', 'setReferrerAction', 'setKarmaAction', 'setPointsAction'))
            ->getMock();

        $sketchController = $this->getMockBuilder('Codebender\ProjectBundle\Controller\SketchController')
            ->disableOriginalConstructor()
            ->setMethods(array('createprojectAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder('Codebender\BoardBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('createBoardsPlanAction'))
            ->getMock();

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue($user));
        $user->expects($this->once())->method('setConfirmationToken')->with($this->equalTo(null));
        $user->expects($this->once())->method('setEnabled')->with($this->equalTo(true));
        $user->expects($this->once())->method('setLastLogin');
        $user->expects($this->once())->method('setRegistrationDate');
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('updateUser')->with($this->equalTo($user));
        $container->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('redirectUrl'));

        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(20),$this->equalTo('REGISTRATION'),$this->equalTo(""));
        $user->expects($this->once())->method('getUsernameCanonical')->will($this->returnValue('username'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getUserAction')->with($this->equalTo('username'))->will($this->returnValue(new Response('{"success":true, "id":1, "referrer_username":"referrer", "referral_code":null}')));
        $container->expects($this->at(5))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(0))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Blink Example"), $this->equalTo($this->first_code))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(6))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(1))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Serial Comm Example"), $this->equalTo($this->second_code))->will($this->returnValue(new Response('{"success":true}')));

        $container->expects($this->at(7))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('createBoardsPlanAction')->with($this->equalTo(1), $this->equalTo('Signup Gift'));

        $container->expects($this->at(8))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(1))->method('getUserAction')->with($this->equalTo('referrer'))->will($this->returnValue(new Response('{"success":false}')));
        $response = $controller->confirmAction('token');
        $this->assertEquals($response->getTargetUrl(), 'redirectUrl');
    }

    public function testConfirmAction_invalidCode()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('setConfirmationToken', 'getId', 'getUsernameCanonical', 'setEnabled', 'setLastLogin', 'setRegistrationDate'))
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder('Codebender\UserBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('getUserAction', 'setReferrerAction', 'setKarmaAction', 'setPointsAction'))
            ->getMock();

        $sketchController = $this->getMockBuilder('Codebender\ProjectBundle\Controller\SketchController')
            ->disableOriginalConstructor()
            ->setMethods(array('createprojectAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder('Codebender\BoardBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('createBoardsPlanAction'))
            ->getMock();

        $codeController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\ReferralCodeController')
            ->disableOriginalConstructor()
            ->setMethods(array('useCodeAction'))
            ->getMock();

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue($user));
        $user->expects($this->once())->method('setConfirmationToken')->with($this->equalTo(null));
        $user->expects($this->once())->method('setEnabled')->with($this->equalTo(true));
        $user->expects($this->once())->method('setLastLogin');
        $user->expects($this->once())->method('setRegistrationDate');
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('updateUser')->with($this->equalTo($user));
        $container->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('redirectUrl'));

        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(20),$this->equalTo('REGISTRATION'),$this->equalTo(""));
        $user->expects($this->once())->method('getUsernameCanonical')->will($this->returnValue('username'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getUserAction')->with($this->equalTo('username'))->will($this->returnValue(new Response('{"success":true, "id":1, "referrer_username":null, "referral_code":"code"}')));
        $container->expects($this->at(5))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(0))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Blink Example"), $this->equalTo($this->first_code))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(6))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(1))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Serial Comm Example"), $this->equalTo($this->second_code))->will($this->returnValue(new Response('{"success":true}')));

        $container->expects($this->at(7))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('createBoardsPlanAction')->with($this->equalTo(1), $this->equalTo('Signup Gift'));

        $container->expects($this->at(8))->method('get')->with($this->equalTo('codebender_utilities.referralcodecontroller'))->will($this->returnValue($codeController));
        $codeController->expects($this->at(0))->method('useCodeAction')->with($this->equalTo('code'))->will($this->returnValue(new Response('{"success":false}')));
        $response = $controller->confirmAction('token');
        $this->assertEquals($response->getTargetUrl(), 'redirectUrl');
    }

    public function testConfirmAction_ValidCode()
    {
        $controller = $this->getMock("Codebender\UserBundle\Controller\RegistrationController", array('setFlash', 'authenticateUser'));

        $container = $this->getMockBuilder("Symfony\Component\DependencyInjection\ContainerInterface")
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'getParameter'))
            ->getMockForAbstractClass();

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('setConfirmationToken', 'getId', 'getUsernameCanonical', 'setEnabled', 'setLastLogin', 'setRegistrationDate'))
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Doctrine\UserManager')
            ->disableOriginalConstructor()
            ->setMethods(array('findUserByConfirmationToken', 'updateUser'))
            ->getMock();
        $controller->setContainer($container);

        $router = $this->getMockBuilder("Symfony\Bundle\FrameworkBundle\Routing\Router")
            ->disableOriginalConstructor()
            ->setMethods(array("generate"))
            ->getMock();

        $logController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\LogController')
            ->disableOriginalConstructor()
            ->setMethods(array('logAction'))
            ->getMock();

        $usercontroller = $this->getMockBuilder('Codebender\UserBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('getUserAction', 'setReferrerAction', 'setKarmaAction', 'setPointsAction'))
            ->getMock();

        $sketchController = $this->getMockBuilder('Codebender\ProjectBundle\Controller\SketchController')
            ->disableOriginalConstructor()
            ->setMethods(array('createprojectAction'))
            ->getMock();

        $boardsController = $this->getMockBuilder('Codebender\BoardBundle\Controller\DefaultController')
            ->disableOriginalConstructor()
            ->setMethods(array('createBoardsPlanAction'))
            ->getMock();

        $codeController = $this->getMockBuilder('Codebender\UtilitiesBundle\Controller\ReferralCodeController')
            ->disableOriginalConstructor()
            ->setMethods(array('useCodeAction'))
            ->getMock();

        $container->expects($this->at(0))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('findUserByConfirmationToken')->with($this->equalTo('token'))->will($this->returnValue($user));
        $user->expects($this->once())->method('setConfirmationToken')->with($this->equalTo(null));
        $user->expects($this->once())->method('setEnabled')->with($this->equalTo(true));
        $user->expects($this->once())->method('setLastLogin');
        $user->expects($this->once())->method('setRegistrationDate');
        $container->expects($this->at(1))->method('get')->with($this->equalTo('fos_user.user_manager'))->will($this->returnValue($userManager));
        $userManager->expects($this->once())->method('updateUser')->with($this->equalTo($user));
        $container->expects($this->at(2))->method('get')->with($this->equalTo('router'))->will($this->returnValue($router));
        $router->expects($this->once())->method('generate')->with($this->equalTo('fos_user_registration_confirmed'))->will($this->returnValue('redirectUrl'));

        $controller->expects($this->once())->method('authenticateUser')->with($this->equalTo($user));
        $container->expects($this->at(3))->method('get')->with($this->equalTo('codebender_utilities.logcontroller'))->will($this->returnValue($logController));
        $user->expects($this->once())->method('getId')->will($this->returnValue(1));
        $logController->expects($this->once())->method('logAction')->with($this->equalTo(1),$this->equalTo(20),$this->equalTo('REGISTRATION'),$this->equalTo(""));
        $user->expects($this->once())->method('getUsernameCanonical')->will($this->returnValue('username'));

        $container->expects($this->at(4))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->at(0))->method('getUserAction')->with($this->equalTo('username'))->will($this->returnValue(new Response('{"success":true, "id":1, "referrer_username":null, "referral_code":"code"}')));
        $container->expects($this->at(5))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(0))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Blink Example"), $this->equalTo($this->first_code))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(6))->method('get')->with($this->equalTo('codebender_project.sketchmanager'))->will($this->returnValue($sketchController));
        $sketchController->expects($this->at(1))->method('createprojectAction')->with($this->equalTo(1), $this->equalTo("Serial Comm Example"), $this->equalTo($this->second_code))->will($this->returnValue(new Response('{"success":true}')));

        $container->expects($this->at(7))->method('get')->with($this->equalTo('codebender_board.defaultcontroller'))->will($this->returnValue($boardsController));
        $boardsController->expects($this->once())->method('createBoardsPlanAction')->with($this->equalTo(1), $this->equalTo('Signup Gift'));

        $container->expects($this->at(8))->method('get')->with($this->equalTo('codebender_utilities.referralcodecontroller'))->will($this->returnValue($codeController));
        $codeController->expects($this->at(0))->method('useCodeAction')->with($this->equalTo('code'))->will($this->returnValue(new Response('{"success":true,"points":40}')));

        $container->expects($this->at(9))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('setKarmaAction')->with($this->equalTo('username'), $this->equalTo(50))->will($this->returnValue(new Response('{"success":true}')));
        $container->expects($this->at(10))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($usercontroller));
        $usercontroller->expects($this->once())->method('setPointsAction')->with($this->equalTo('username'), $this->equalTo(40))->will($this->returnValue(new Response('{"success":true}')));

        $response = $controller->confirmAction('token');
        $this->assertEquals($response->getTargetUrl(), 'redirectUrl');
    }


}
