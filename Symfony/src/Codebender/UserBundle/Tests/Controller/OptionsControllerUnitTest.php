<?php

namespace Codebender\UserBundle\Tests\Controller;

use Codebender\UserBundle\Controller\OptionsController;
use Symfony\Component\HttpFoundation\Response;
use Codebender\UserBundle\Form\Type\OptionsFormType;

class PrivateOptionsControllerTester extends OptionsController
{
    public function call_isCurrentPass($currentPassword)
    {
        return $this->isCurrentPass($currentPassword);
    }

    public function call_comparePassword($currentPassword)
    {
        return $this->comparePassword($currentPassword);
    }

    public function call_getErrorMessages($form)
    {
        return $this->getErrorMessages($form);
    }
}
class OptionsControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsEditAction_renderForm()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'getLastname', 'getEmail', 'getTwitter'))
            ->getMock();


        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('createView', 'getName', 'get'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getName'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastename'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData')->will($this->returnValue('username'));
        $formInterface->expects($this->at(1))->method('setData')->will($this->returnValue('firstname'));
        $formInterface->expects($this->at(2))->method('setData')->will($this->returnValue('lastname'));
        $formInterface->expects($this->at(3))->method('setData')->will($this->returnValue('email'));
        $formInterface->expects($this->at(4))->method('setData')->will($this->returnValue('twitter'));

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $templating->expects($this->once())->method('render')->with($this->equalTo('CodebenderUserBundle:Default:options.html.twig'))->will($this->returnValue('rendered'));

        $form->expects($this->once())->method('createView');

        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), 'rendered');
    }
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testOptionsEditAction_NoUser()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'createForm'));

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue(NULL));

        $controller->optionsEditAction();

    }

    public function testOptionsEditAction_inValidForm()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'getLastname', 'getEmail', 'getTwitter'))
            ->getMock();


        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastename'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue(''));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('sample@email.com'));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response('true')));
        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue(""));

        $form->expects($this->once())->method('isValid')->will($this->returnValue(false));

        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array()));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:red; font-weight:bold\"><i class=\"icon-remove-sign icon-large\"><\/i> ERROR:<\/span> Your Profile was <strong>NOT updated<\/strong>, please fix the errors and try again."}');
    }

    public function testOptionsEditAction_NonSensitive()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'setFirstname', 'getLastname', 'setLastname', 'getEmail', 'getTwitter', 'setTwitter'))
            ->getMock();

        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue(''));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('sample@email.com'));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response('true')));
        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue(""));

        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $em->expects($this->once())->method('persist')->with($this->equalTo($user));

        $form->expects($this->at(10))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(9))->method('getData')->will($this->returnValue("newFirstname"));
        $user->expects($this->at(7))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(8))->method('setFirstname')->with($this->equalTo('newFirstname'));


        $form->expects($this->at(11))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(10))->method('getData')->will($this->returnValue("newLastname"));
        $user->expects($this->at(9))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(10))->method('setLastname')->with($this->equalTo('newLastname'));

        $form->expects($this->at(12))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(11))->method('getData')->will($this->returnValue("newTwitter"));
        $user->expects($this->at(11))->method('getTwitter')->will($this->returnValue('twitter'));
        $user->expects($this->at(12))->method('setTwitter')->with($this->equalTo('newTwitter'));

        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $em->expects($this->once())->method('flush');
        $userManager->expects($this->once())->method('reloadUser')->with($this->equalTo($user));
        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array()));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}');
    }

    public function testOptionsEditAction_Sensitive_Email()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'isCurrentPass', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'setFirstname', 'getLastname', 'setLastname', 'getEmail', 'setEmail', 'getTwitter', 'setTwitter'))
            ->getMock();

        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();

        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue('MyP@ssword'));
        $controller->expects($this->once())->method('isCurrentPass')->with($this->equalTo('MyP@ssword'))->will($this->returnValue(true));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('newsample@email.com'));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('newsample@email.com'))->will($this->returnValue(new Response('false')));


        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue(""));

        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $em->expects($this->once())->method('persist')->with($this->equalTo($user));

        $form->expects($this->at(10))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(9))->method('getData')->will($this->returnValue("newFirstname"));
        $user->expects($this->at(6))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(7))->method('setFirstname')->with($this->equalTo('newFirstname'));


        $form->expects($this->at(11))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(10))->method('getData')->will($this->returnValue("newLastname"));
        $user->expects($this->at(8))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(9))->method('setLastname')->with($this->equalTo('newLastname'));

        $form->expects($this->at(12))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(11))->method('getData')->will($this->returnValue("newTwitter"));
        $user->expects($this->at(10))->method('getTwitter')->will($this->returnValue('twitter'));
        $user->expects($this->at(11))->method('setTwitter')->with($this->equalTo('newTwitter'));

        $user->expects($this->at(12))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(12))->method('setEmail')->with($this->equalTo('newsample@email.com'));
        $em->expects($this->once())->method('flush');
        $userManager->expects($this->once())->method('reloadUser')->with($this->equalTo($user));
        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array()));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}');
    }

    public function testOptionsEditAction_Sensitive_Password()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'isCurrentPass', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'setFirstname', 'getLastname', 'setLastname', 'getEmail', 'setEmail', 'getTwitter', 'setTwitter'))
            ->getMock();


        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue('MyP@ssword'));
        $controller->expects($this->once())->method('isCurrentPass')->with($this->equalTo('MyP@ssword'))->will($this->returnValue(true));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('sample@email.com'));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response('true')));
        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue("newPASSWORD!"));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('validator'))->will($this->returnValue($validator));
        $validator->expects($this->once())->method('validateValue')->with($this->equalTo("newPASSWORD!"))->will($this->returnValue(array()));


        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $em->expects($this->once())->method('persist')->with($this->equalTo($user));

        $form->expects($this->at(10))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(9))->method('getData')->will($this->returnValue("newFirstname"));
        $user->expects($this->at(7))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(8))->method('setFirstname')->with($this->equalTo('newFirstname'));


        $form->expects($this->at(11))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(10))->method('getData')->will($this->returnValue("newLastname"));
        $user->expects($this->at(9))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(10))->method('setLastname')->with($this->equalTo('newLastname'));

        $form->expects($this->at(12))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(11))->method('getData')->will($this->returnValue("newTwitter"));
        $user->expects($this->at(11))->method('getTwitter')->will($this->returnValue('twitter'));
        $user->expects($this->at(12))->method('setTwitter')->with($this->equalTo('newTwitter'));

        $form->expects($this->at(13))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(12))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(13))->method('getData')->will($this->returnValue("newPASSWORD!"));
        $user->expects($this->at(12))->method('setPlainPassword')->will($this->returnValue('newPASSWORD!'));
        $userManager->expects($this->once())->method('updatePassword')->with($this->equalTo($user));
        $em->expects($this->once())->method('flush');
        $userManager->expects($this->once())->method('reloadUser')->with($this->equalTo($user));
        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array()));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!"}');
    }

    public function testOptionsEditAction_Sensitive_PasswordInvalid()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'isCurrentPass', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'setFirstname', 'getLastname', 'setLastname', 'getEmail', 'setEmail', 'getTwitter', 'setTwitter'))
            ->getMock();


        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'addError', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMockForAbstractClass();

        $error = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')
            ->disableOriginalConstructor()
            ->setMethods(array('getMessage'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue('MyP@ssword'));
        $controller->expects($this->once())->method('isCurrentPass')->with($this->equalTo('MyP@ssword'))->will($this->returnValue(true));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('sample@email.com'));

        $controller->expects($this->at(3))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response('true')));
        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue("<invalid!"));
        $controller->expects($this->at(4))->method('get')->with($this->equalTo('validator'))->will($this->returnValue($validator));
        $validator->expects($this->once())->method('validateValue')->with($this->equalTo("<invalid!"))->will($this->returnValue(array($error)));
        $error->expects($this->once())->method('getMessage')->will($this->returnValue('Validation Error'));
        $form->expects($this->at(9))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(9))->method('addError');

        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $em->expects($this->once())->method('persist')->with($this->equalTo($user));

        $form->expects($this->at(11))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(10))->method('getData')->will($this->returnValue("newFirstname"));
        $user->expects($this->at(7))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(8))->method('setFirstname')->with($this->equalTo('newFirstname'));


        $form->expects($this->at(12))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(11))->method('getData')->will($this->returnValue("newLastname"));
        $user->expects($this->at(9))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(10))->method('setLastname')->with($this->equalTo('newLastname'));

        $form->expects($this->at(13))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(12))->method('getData')->will($this->returnValue("newTwitter"));
        $user->expects($this->at(11))->method('getTwitter')->will($this->returnValue('twitter'));
        $user->expects($this->at(12))->method('setTwitter')->with($this->equalTo('newTwitter'));


        $em->expects($this->once())->method('flush');
        $userManager->expects($this->once())->method('reloadUser')->with($this->equalTo($user));
        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array('plainPassword' => 'Validation Error')));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!","plainPassword_confirm":"Validation Error"}');
    }

    public function testOptionsEditAction_Sensitive_PasswordNotSubmittedOld()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get', 'isCurrentPass', 'getErrorMessages' ,'createForm'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getUsername', 'getFirstname', 'setFirstname', 'getLastname', 'setLastname', 'getEmail', 'setEmail', 'getTwitter', 'setTwitter'))
            ->getMock();


        $utilitiesHandler = $this->getMockBuilder('Codebender\UtilitiesBundle\Handler\DefaultHandler')
            ->disableOriginalConstructor()
            ->setMethods(array('get_gravatar'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('handleRequest', 'getName', 'get', 'isValid'))
            ->getMock();


        $formInterface = $this->getMockBuilder('Symfony\Component\Form\AbstractType')
            ->disableOriginalConstructor()
            ->setMethods(array('setData', 'getData', 'getName', 'addError', 'get'))
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator')
            ->disableOriginalConstructor()
            ->setMethods(array('validateValue'))
            ->getMockForAbstractClass();

        $error = $this->getMockBuilder('Symfony\Component\Validator\ConstraintViolation')
            ->disableOriginalConstructor()
            ->setMethods(array('getMessage'))
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));

        $controller->expects($this->at(0))->method('get')->with($this->equalTo('codebender_utilities.handler'))->will($this->returnValue($utilitiesHandler));
        $user->expects($this->at(0))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $utilitiesHandler->expects($this->once())->method('get_gravatar')->with($this->equalTo('sample@email.com'), $this->equalTo(120))->will($this->returnValue('gravatar_url'));

        $controller->expects($this->once())->method('createForm')->with($this->equalTo(new OptionsFormType()))->will($this->returnValue($form));

        $form->expects($this->at(0))->method('get')->with($this->equalTo('username'))->will($this->returnValue($formInterface));
        $form->expects($this->at(1))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(2))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $form->expects($this->at(3))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $form->expects($this->at(4))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));

        $user->expects($this->at(1))->method('getUsername')->will($this->returnValue('username'));
        $user->expects($this->at(2))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(3))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(4))->method('getEmail')->will($this->returnValue('sample@email.com'));
        $user->expects($this->at(5))->method('getTwitter')->will($this->returnValue('twitter'));

        $formInterface->expects($this->at(0))->method('setData');
        $formInterface->expects($this->at(1))->method('setData');
        $formInterface->expects($this->at(2))->method('setData');
        $formInterface->expects($this->at(3))->method('setData');
        $formInterface->expects($this->at(4))->method('setData');

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));

        $form->expects($this->once())->method('handleRequest')->with($this->equalTo($request));
        $form->expects($this->at(6))->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(5))->method('getData')->will($this->returnValue(''));

        $form->expects($this->at(7))->method('get')->with($this->equalTo('email'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(6))->method('getData')->will($this->returnValue('sample@email.com'));

        $controller->expects($this->at(2))->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response('true')));
        $user->expects($this->at(6))->method('getEmail')->will($this->returnValue('sample@email.com'));

        $form->expects($this->at(8))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(7))->method('get')->with($this->equalTo('new'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(8))->method('getData')->will($this->returnValue("newPass@"));
        $form->expects($this->at(9))->method('get')->with($this->equalTo('plainPassword'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(9))->method('addError');

        $form->expects($this->once())->method('isValid')->will($this->returnValue(true));

        $em->expects($this->once())->method('persist')->with($this->equalTo($user));

        $form->expects($this->at(11))->method('get')->with($this->equalTo('firstname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(10))->method('getData')->will($this->returnValue("newFirstname"));
        $user->expects($this->at(7))->method('getFirstname')->will($this->returnValue('firstname'));
        $user->expects($this->at(8))->method('setFirstname')->with($this->equalTo('newFirstname'));


        $form->expects($this->at(12))->method('get')->with($this->equalTo('lastname'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(11))->method('getData')->will($this->returnValue("newLastname"));
        $user->expects($this->at(9))->method('getLastname')->will($this->returnValue('lastname'));
        $user->expects($this->at(10))->method('setLastname')->with($this->equalTo('newLastname'));

        $form->expects($this->at(13))->method('get')->with($this->equalTo('twitter'))->will($this->returnValue($formInterface));
        $formInterface->expects($this->at(12))->method('getData')->will($this->returnValue("newTwitter"));
        $user->expects($this->at(11))->method('getTwitter')->will($this->returnValue('twitter'));
        $user->expects($this->at(12))->method('setTwitter')->with($this->equalTo('newTwitter'));


        $em->expects($this->once())->method('flush');
        $userManager->expects($this->once())->method('reloadUser')->with($this->equalTo($user));
        $controller->expects($this->once())->method('getErrorMessages')->with($this->equalTo($form))->will($this->returnValue(array('plainPassword' => 'Please provide your Current Password along with your New one.')));
        $response = $controller->optionsEditAction();
        $this->assertEquals($response->getContent(), '{"message":"<span style=\"color:green; font-weight:bold\"><i class=\"icon-ok-sign icon-large\"><\/i> SUCCESS:<\/span> Profile Updated!","plainPassword_confirm":"Please provide your Current Password along with your New one."}');
    }


    public function testIsCurrentPasswordAction_yes()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('comparePassword'));
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $request->expects($this->once())->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue('passWord!'));
        $controller->expects($this->once())->method('comparePassword')->with($this->equalTo('passWord!'))->will($this->returnValue(true));
        $response = $controller->isCurrentPasswordAction();
        $this->assertEquals($response->getContent(), '{"valid":true}' );
    }

    public function testIsCurrentPasswordAction_no()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('comparePassword'));
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $request->expects($this->once())->method('get')->with($this->equalTo('currentPassword'))->will($this->returnValue('passWord!'));
        $controller->expects($this->once())->method('comparePassword')->with($this->equalTo('passWord!'))->will($this->returnValue(false));
        $response = $controller->isCurrentPasswordAction();
        $this->assertEquals($response->getContent(), '{"valid":false}' );
    }

    public function testIsCurrentPasswordAction_wrongMethod()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('comparePassword'));
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $response = $controller->isCurrentPasswordAction();
        $this->assertNull($response);
    }

    public function testIsEmailAvailbleAction_Yes()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $request->expects($this->once())->method('get')->with($this->equalTo('email'))->will($this->returnValue('sample@email.com'));
        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response("false")));

        $response = $controller->isEmailAvailableAction();

        $this->assertEquals($response->getContent(), '{"valid":"available"}' );
    }

    public function testIsEmailAvailbleAction_No()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $request->expects($this->once())->method('get')->with($this->equalTo('email'))->will($this->returnValue('sample@email.com'));
        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response("true")));
        $user->expects($this->once())->method('getEmail')->will($this->returnValue('anotherSample@email.com'));

        $response = $controller->isEmailAvailableAction();

        $this->assertEquals($response->getContent(), '{"valid":"inUse"}' );
    }


    public function testIsEmailAvailbleAction_Own()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('get'));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $userController = $this->getMockBuilder('Codebender\UserBundle\Controller')
            ->disableOriginalConstructor()
            ->setMethods(array('emailExistsAction'))
            ->getMock();

        $request->expects($this->once())->method('getMethod')->will($this->returnValue('POST'));
        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $request->expects($this->once())->method('get')->with($this->equalTo('email'))->will($this->returnValue('sample@email.com'));
        $controller->expects($this->once())->method('get')->with($this->equalTo('codebender_user.usercontroller'))->will($this->returnValue($userController));
        $userController->expects($this->once())->method('emailExistsAction')->with($this->equalTo('sample@email.com'))->will($this->returnValue(new Response("true")));
        $user->expects($this->once())->method('getEmail')->will($this->returnValue('sample@email.com'));

        $response = $controller->isEmailAvailableAction();

        $this->assertEquals($response->getContent(), '{"valid":"own"}' );
    }

    public function testIsEmailAvaialbleAction_WrongMethod()
    {
        $controller = $this->setUpController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);
        $request->expects($this->once())->method('getMethod')->will($this->returnValue('GET'));
        $response = $controller->isEmailAvailableAction();
        $this->assertNull($response);
    }
    public function testIsCurrentPass_true()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('comparePassword'));
        $controller->expects($this->once())->method('comparePassword')->with($this->equalTo('passWord!'))->will($this->returnValue(true));

        $response = $controller->call_isCurrentPass('passWord!');
        $this->assertTrue($response);
    }

    public function testIsCurrentPass_false()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, array('comparePassword'));
        $controller->expects($this->once())->method('comparePassword')->with($this->equalTo('passWord!'))->will($this->returnValue(false));

        $response = $controller->call_isCurrentPass('passWord!');
        $this->assertFalse($response);
    }

    public function testComparePassword_yes()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getPassword', 'getSalt'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $passInterface = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $encoderFactory->expects($this->once())->method('getEncoder')->with($this->equalTo($user))->will($this->returnValue($passInterface));
        $user->expects($this->once())->method('getSalt')->will($this->returnValue('pepper'));
        $passInterface->expects($this->once())->method('encodePassword')->with($this->equalTo('passWord!'), $this->equalTo('pepper'))->will($this->returnValue('encodedPas$'));

        $user->expects($this->once())->method('getPassword')->will($this->returnValue('encodedPas$'));

        $this->assertTrue($controller->call_comparePassword('passWord!'));

    }

    public function testComparePassword_no()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getPassword', 'getSalt'))
            ->getMock();

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $passInterface = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $security->expects($this->once())->method('getToken')->will($this->returnValue($token));
        $token->expects($this->once())->method('getUser')->will($this->returnValue($user));
        $encoderFactory->expects($this->once())->method('getEncoder')->with($this->equalTo($user))->will($this->returnValue($passInterface));
        $user->expects($this->once())->method('getSalt')->will($this->returnValue('pepper'));
        $passInterface->expects($this->once())->method('encodePassword')->with($this->equalTo('passWord!'), $this->equalTo('pepper'))->will($this->returnValue('encodedPas$'));

        $user->expects($this->once())->method('getPassword')->will($this->returnValue('@notherEncodedPas$'));

        $this->assertFalse($controller->call_comparePassword('passWord!'));

    }

    public function testGetErrorMessages_empty()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getErrors', 'getName', 'count'))
            ->getMock();

        $form->expects($this->once())->method('getErrors')->will($this->returnValue(array()));
        $form->expects($this->once())->method('count')->will($this->returnValue(0));

        $response = $controller->call_getErrorMessages($form);

        $this->assertEquals($response, array());

    }

    public function testGetErrorMessages_errors()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getErrors', 'getName', 'count'))
            ->getMock();

        $error = $this->getMockBuilder('Symfony\Component\Form\FormError')
            ->disableOriginalConstructor()
            ->setMethods(array('getMessageTemplate', 'getMessageParameters'))
            ->getMock();

        $form->expects($this->once())->method('getErrors')->will($this->returnValue(array($error)));

        $error->expects($this->once()) -> method('getMessageTemplate')->will($this->returnValue('message template with parameterVar'));
        $error->expects($this->once()) -> method('getMessageParameters')->will($this->returnValue(array('parameterVar' => 'parameterValue')));
        $form->expects($this->once())->method('count')->will($this->returnValue(0));

        $response = $controller->call_getErrorMessages($form);

        $this->assertEquals($response, array('0' => 'message template with parameterValue'));

    }

    public function testGetErrorMessages_children()
    {
        $controller = $this->setUpPrivateTesterController($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid, NULL);
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getErrors', 'getName', 'count', 'all'))
            ->getMock();

        $child = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->setMethods(array('getErrors', 'getName', 'isValid', 'count'))
            ->getMock();

        $error = $this->getMockBuilder('Symfony\Component\Form\FormError')
            ->disableOriginalConstructor()
            ->setMethods(array('getMessageTemplate', 'getMessageParameters'))
            ->getMock();

        $form->expects($this->once())->method('getErrors')->will($this->returnValue(array()));
        $form->expects($this->once())->method('count')->will($this->returnValue(1));
        $form->expects($this->once())->method('all')->will($this->returnValue(array($child)));

        $child->expects($this->once())->method('isValid')->will($this->returnValue(false));
        $child->expects($this->once())->method('getName')->will($this->returnValue('child'));

        $child->expects($this->once())->method('getErrors')->will($this->returnValue(array($error)));

        $error->expects($this->once()) -> method('getMessageTemplate')->will($this->returnValue('message template with parameterVar'));
        $error->expects($this->once()) -> method('getMessageParameters')->will($this->returnValue(array('parameterVar' => 'parameterValue')));


        $response = $controller->call_getErrorMessages($form);

        $this->assertEquals($response, array('child' => array('0' => 'message template with parameterValue')));

    }


    private function initArguments(&$templating, &$security, &$container,  &$request, &$userManager, &$encoderFactory, &$em, &$listapi, &$listid)
    {

        $templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $userManager = $this->getMockBuilder('FOS\UserBundle\Model\UserManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $encoderFactory = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\EncoderFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $listapi = 'MockListApi';

        $listid = 'MockListId';
    }

    private function setUpController(&$templating, &$security, &$container,  &$request, &$userManager, &$encoderFactory, &$em, &$listapi, &$listid, $m)
    {
        $this->initArguments($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid);
        $controller = $this->getMock('Codebender\UserBundle\Controller\OptionsController', $methods = $m, $arguments = array($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid) );
        return $controller;
    }

    private function setUpPrivateTesterController(&$templating, &$security, &$container,  &$request, &$userManager, &$encoderFactory, &$em, &$listapi, &$listid, $m)
    {
        $this->initArguments($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid);
        $controller = $this->getMock('Codebender\UserBundle\Tests\Controller\PrivateOptionsControllerTester', $methods = $m, $arguments = array($templating, $security, $container, $request, $userManager, $encoderFactory, $em, $listapi, $listid) );
        return $controller;
    }
}
