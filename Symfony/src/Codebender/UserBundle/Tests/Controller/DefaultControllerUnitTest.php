<?php

namespace Codebender\UserBundle\Tests\Controller;
use Codebender\UserBundle\Controller\DefaultController;
use Codebender\UserBundle\Entity\User;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerUnitTest extends \PHPUnit_Framework_TestCase
{
	public function testExistsAction_Exists()
	{
		$this->initArguments($templating, $security, $em, $container);

		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getUserAction"), array($templating, $security, $em, $container));

		$controller->expects($this->once())->method('getUserAction')->with($this->equalTo("iamfake"))->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$response = $controller->existsAction("iamfake");
		$this->assertEquals($response->getContent(), 'true');
	}

	public function testExistsAction_NoUser()
	{
		$this->initArguments($templating, $security, $em, $container);

		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController",array("getUserAction"), array($templating, $security, $em, $container));

		$controller->expects($this->once())->method('getUserAction')->with($this->equalTo("idontexist"))->will($this->returnValue(new Response('{"success":false}')));

		$response = $controller->existsAction("idontexist");
		$this->assertEquals($response->getContent(), 'false');
	}

	public function testEmailExistsAction_EmailExists()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByEmail"))
			->getMock();

		$repo->expects($this->once())->method('findOneByEmail')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->emailExistsAction("iamfake");
		$this->assertEquals($response->getContent(), 'true');
	}

	public function testEmailExistsAction_NoEmail()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByEmail"))
			->getMock();

		$repo->expects($this->once())->method('findOneByEmail')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->emailExistsAction("idontexist");
		$this->assertEquals($response->getContent(), 'false');
	}

	public function testGetUserAction_UserExists()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$user->expects($this->once())->method('getId')->will($this->returnValue(1));
		$user->expects($this->once())->method('getEmail')->will($this->returnValue("a@fake.email"));
		$user->expects($this->once())->method('getUsername')->will($this->returnValue("iamfake"));
		$user->expects($this->once())->method('getFirstname')->will($this->returnValue("fake"));
		$user->expects($this->once())->method('getLastname')->will($this->returnValue("basterd"));
		$user->expects($this->once())->method('getTwitter')->will($this->returnValue("atwitteraccount"));
		$user->expects($this->once())->method('getKarma')->will($this->returnValue(150));
		$user->expects($this->once())->method('getPoints')->will($this->returnValue(150));
		$user->expects($this->once())->method('getReferrals')->will($this->returnValue(5));
		$user->expects($this->once())->method('getReferrerUsername')->will($this->returnValue(null));
		$user->expects($this->once())->method('getReferralCode')->will($this->returnValue(null));
        $user->expects($this->once())->method('getWalkthroughStatus')->will($this->returnValue(0));
        $user->expects($this->once())->method('getEula')->will($this->returnValue(1));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->getUserAction("iamfake");
		$this->assertEquals($response->getContent(), '{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0,"last_walkthrough_date":null,"preregistration_date":null,"registration_date":null,"actual_last_login":null,"edit_count":null,"last_edit":null,"compile_count":null,"last_compile":null,"flash_count":null,"last_flash":null,"cloning_count":null,"last_cloning":null,"eula":1}');
	}

	public function testGetUserAction_NoUser()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->getUserAction("idontexist");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testGetCurrentUserAction_userLoggedIn()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$user->expects($this->once())->method('getUsername')->will($this->returnValue("iamfake"));

		$token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
			->disableOriginalConstructor()
			->getMock();

		$this->initArguments($templating, $security, $em, $container);

		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getUserAction')->with($this->equalTo("iamfake"))->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$token->expects($this->once())->method('getUser')->will($this->returnValue($user));
		$security->expects($this->any())->method('getToken')->will($this->returnValue($token));

		$response = $controller->getCurrentUserAction();
		$this->assertEquals($response->getContent(), '{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}');
	}

	/**
	 * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function testGetCurrentUserAction_userNotFound()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$user->expects($this->once())->method('getUsername')->will($this->returnValue("idontexist"));

		$token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
			->disableOriginalConstructor()
			->getMock();

		$token->expects($this->once())->method('getUser')->will($this->returnValue($user));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getUserAction')->with($this->equalTo("idontexist"))->will($this->returnValue(new Response('{"success":false}')));

		$security->expects($this->any())->method('getToken')->will($this->returnValue($token));

		$controller->getCurrentUserAction();
	}

	public function testGetCurrentUserAction_userAnonymous()
	{

		$token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
			->disableOriginalConstructor()
			->getMock();

		$token->expects($this->once())->method('getUser')->will($this->returnValue("anon."));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$security->expects($this->any())->method('getToken')->will($this->returnValue($token));

		$response = $controller->getCurrentUserAction();
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testSearchAction_NameExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50}}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50}}');
	}

	public function testSearchAction_UsernameExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"afirstname","lastname":"alastname","username":"search_string","karma":50}}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"afirstname","lastname":"alastname","username":"search_string","karma":50}}');
	}

	public function testSearchAction_TwitterExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}');
	}

	public function testSearchAction_NameUsernameExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50}}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"firstname":"afirstname","lastname":"alastname","username":"search_string","karma":50}}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50},"2":{"firstname":"afirstname","lastname":"alastname","username":"search_string","karma":50}}');
	}

	public function testSearchAction_NameTwitterExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50}}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname","username":"ausername","karma":50},"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}');
	}

	public function testSearchAction_UsernameTwitterExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"search_string","karma":50}}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname","username":"search_string","karma":50},"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}');
	}

	public function testSearchAction_AllExist()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"search_string","karma":50}}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"1":{"firstname":"search_string","lastname":"alastname","username":"search_string","karma":50}}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname","username":"search_string","karma":50},"2":{"firstname":"afirstname","lastname":"alastname","username":"ausername","karma":50}}');
	}

	public function testSearchAction_NoneExists()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("searchNameAction", "searchUsernameAction", "searchTwitterAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('searchNameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchUsernameAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$controller->expects($this->once())->method('searchTwitterAction')->with($this->equalTo("search_string"))->will($this->returnValue(new Response('{}')));
		$response = $controller->searchAction("search_string");
		$this->assertEquals($response->getContent(), "[]");
	}

	public function testSearchNameAction_NoneExists()
	{
		$users = array();

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult", "setMaxResults"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.firstname LIKE :name OR u.lastname LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchNameAction("search_string");
		$this->assertEquals($response->getContent(), '[]');
	}

	public function testSearchNameAction_TwoExist()
	{
		$users = array();
		for ($i = 0; $i < 2; $i++)
		{
			$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
				->disableOriginalConstructor()
				->getMock();

			$user->expects($this->once())->method('getId')->will($this->returnValue($i+1));
			$user->expects($this->once())->method('getFirstname')->will($this->returnValue("search_string"));
			$user->expects($this->once())->method('getLastname')->will($this->returnValue("alastname".$i));
			$user->expects($this->once())->method('getUsername')->will($this->returnValue("ausername".$i));
			$user->expects($this->once())->method('getKarma')->will($this->returnValue(50));
			$users[] = $user;
		}

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult", "setMaxResults"))
			->getMock();
        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.firstname LIKE :name OR u.lastname LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchNameAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"search_string","lastname":"alastname0","username":"ausername0","karma":50},"2":{"firstname":"search_string","lastname":"alastname1","username":"ausername1","karma":50}}');
	}

	public function testSearchUsernameAction_NoneExists()
	{
		$users = array();

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult", "setMaxResults"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));
        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.username LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchUsernameAction("search_string");
		$this->assertEquals($response->getContent(), '[]');
	}

	public function testSearchUsernameAction_TwoExist()
	{
		$users = array();
		for ($i = 0; $i < 2; $i++)
		{
			$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
				->disableOriginalConstructor()
				->getMock();

			$user->expects($this->once())->method('getId')->will($this->returnValue($i + 1));
			$user->expects($this->once())->method('getFirstname')->will($this->returnValue("afirstname".$i));
			$user->expects($this->once())->method('getLastname')->will($this->returnValue("alastname".$i));
			$user->expects($this->once())->method('getUsername')->will($this->returnValue("search_string".$i));
			$user->expects($this->once())->method('getKarma')->will($this->returnValue(50));
			$users[] = $user;
		}

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult", "setMaxResults"))
			->getMock();
        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));
        $query->expects($this->once())->method('getResult')->will($this->returnValue($users));


		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.username LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchUsernameAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"afirstname0","lastname":"alastname0","username":"search_string0","karma":50},"2":{"firstname":"afirstname1","lastname":"alastname1","username":"search_string1","karma":50}}');
	}

	public function testSearchTwitterAction_NoneExists()
	{
		$users = array();

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult", "setMaxResults"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));
        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.twitter LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchTwitterAction("search_string");
		$this->assertEquals($response->getContent(), '[]');
	}

	public function testSearchTwitterAction_TwoExist()
	{
		$users = array();
		for ($i = 0; $i < 2; $i++)
		{
			$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
				->disableOriginalConstructor()
				->getMock();

			$user->expects($this->once())->method('getId')->will($this->returnValue($i + 1));
			$user->expects($this->once())->method('getFirstname')->will($this->returnValue("afirstname".$i));
			$user->expects($this->once())->method('getLastname')->will($this->returnValue("alastname".$i));
			$user->expects($this->once())->method('getUsername')->will($this->returnValue("ausername".$i));
			$user->expects($this->once())->method('getKarma')->will($this->returnValue(50));
			$users[] = $user;
		}

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult","setMaxResults"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));
        $query->expects($this->once())->method('setMaxResults')->with($this->equalTo(1000))->will($this->returnValue($query));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery", "setParameter"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.twitter LIKE :name'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setParameter')->with($this->equalTo('name'), $this->equalTo('%search_string%'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->searchTwitterAction("search_string");
		$this->assertEquals($response->getContent(), '{"1":{"firstname":"afirstname0","lastname":"alastname0","username":"ausername0","karma":50},"2":{"firstname":"afirstname1","lastname":"alastname1","username":"ausername1","karma":50}}');
	}

	public function testSetReferrerAction_Success()
	{
		$referrer = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$referrer->expects($this->once())->method('getReferrals')->will($this->returnValue(5));
		$referrer->expects($this->once())->method('setReferrals')->with($this->equalTo(6));
		$referrer->expects($this->once())->method('getKarma')->will($this->returnValue(50));
		$referrer->expects($this->once())->method('setKarma')->with($this->equalTo(70));
		$referrer->expects($this->once())->method('getPoints')->will($this->returnValue(60));
		$referrer->expects($this->once())->method('setPoints')->with($this->equalTo(80));

		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('setReferrer')->with($this->equalTo($referrer));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->at(0))->method('findOneByUsername')->with($this->equalTo("fakeuser"))->will($this->returnValue($user));
		$repo->expects($this->at(1))->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue($referrer));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->exactly(2))->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush');

		$response = $controller->setReferrerAction("fakeuser", "idontexist");
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetReferrerAction_NoUser()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->setReferrerAction("idontexist", "areferrer");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testSetReferrerAction_NoReferrer()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();

		$repo->expects($this->at(0))->method('findOneByUsername')->with($this->equalTo("fakeuser"))->will($this->returnValue($user));
		$repo->expects($this->at(1))->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->exactly(2))->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->setReferrerAction("fakeuser", "idontexist");
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testSetKarmaAction_Success()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('setKarma')->with($this->equalTo(50));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush');

		$response = $controller->setKarmaAction("iamfake", 50);
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetKarmaAction_NoUser()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->setKarmaAction("idontexist", 50);
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testSetPointsAction_Success()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('setPoints')->with($this->equalTo(50));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush');

		$response = $controller->setPointsAction("iamfake", 50);
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetWalkthroughStatusAction_userLoggedIn_StatusSmallerThanExisting()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('getWalkthroughStatus')->will($this->returnValue(3));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->setWalkthroughStatusAction(3);
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetWalkthroughStatusAction_userLoggedIn_SuccessNotCompleted()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('getWalkthroughStatus')->will($this->returnValue(3));
		$user->expects($this->once())->method('setWalkthroughStatus')->with($this->equalTo(4));
		$user->expects($this->never())->method('getPoints');
		$user->expects($this->never())->method('setPoints');

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush');

		$response = $controller->setWalkthroughStatusAction(4);
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetWalkthroughStatusAction_userLoggedIn_CompleteSuccess()
	{
		$user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
			->disableOriginalConstructor()
			->getMock();
		$user->expects($this->once())->method('getWalkthroughStatus')->will($this->returnValue(4));
		$user->expects($this->once())->method('setWalkthroughStatus')->with($this->equalTo(5));
		$user->expects($this->once())->method('getPoints')->will($this->returnValue(50));
		$user->expects($this->once())->method('setPoints')->with($this->equalTo(100));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
		$em->expects($this->once())->method('flush');

		$response = $controller->setWalkthroughStatusAction(5);
		$this->assertEquals($response->getContent(), '{"success":true}');
	}

	public function testSetWalkthroughStatusAction_userAnonymous()
	{
		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

		$response = $controller->setWalkthroughStatusAction(5);
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

    public function testSetEulaAction_LoggedIn()
    {
        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();
        $user->expects($this->once())->method('setEula')->with($this->equalTo(true));

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("findOneByUsername"))
            ->getMock();
        $repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $em->expects($this->once())->method('flush');

        $response = $controller->setEulaAction();
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetEulaAction_userAnonymous()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->setEulaAction();
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testUpdateEditAction_LoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getEditCount','setEditCount','setLastEdit'))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("findOneByUsername"))
            ->getMock();

        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

        $user->expects($this->once())->method('getEditCount')->will($this->returnValue(1));
        $user->expects($this->once())->method('setEditCount')->with($this->equalTo(2));
        $user->expects($this->once())->method('setLastEdit');
        $em->expects($this->once())->method('flush');

        $response = $controller->updateEditAction();
        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testUpdateEditAction_NotLoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->updateEditAction();
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    public function testUpdateCompileAction_LoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getCompileCount','setCompileCount','setLastCompile'))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("findOneByUsername"))
            ->getMock();

        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

        $user->expects($this->once())->method('getCompileCount')->will($this->returnValue(1));
        $user->expects($this->once())->method('setCompileCount')->with($this->equalTo(2));
        $user->expects($this->once())->method('setLastCompile');
        $em->expects($this->once())->method('flush');

        $response = $controller->updateCompileAction();
        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testUpdateCompileAction_NotLoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->updateCompileAction();
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    public function testUpdateFlashAction_LoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getFlashCount','setFlashCount','setLastFlash'))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("findOneByUsername"))
            ->getMock();

        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

        $user->expects($this->once())->method('getFlashCount')->will($this->returnValue(1));
        $user->expects($this->once())->method('setFlashCount')->with($this->equalTo(2));
        $user->expects($this->once())->method('setLastFlash');
        $em->expects($this->once())->method('flush');

        $response = $controller->updateFlashAction();
        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testUpdateFlashAction_NotLoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->updateFlashAction();
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    public function testUpdateCloningAction_LoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

        $user = $this->getMockBuilder('Codebender\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getCloningCount','setCloningCount','setLastCloning'))
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("findOneByUsername"))
            ->getMock();

        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));
        $repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("iamfake"))->will($this->returnValue($user));

        $user->expects($this->once())->method('getCloningCount')->will($this->returnValue(1));
        $user->expects($this->once())->method('setCloningCount')->with($this->equalTo(2));
        $user->expects($this->once())->method('setLastCloning');
        $em->expects($this->once())->method('flush');

        $response = $controller->updateCloningAction();
        $this->assertEquals($response->getContent(), '{"success":true}');

    }

    public function testUpdateCloningAction_NotLoggedIn()
    {
        $this->initArguments($templating, $security, $em, $container);
        $controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));
        $controller->expects($this->once())->method('getCurrentUserAction')->will($this->returnValue(new Response('{"success":false}')));

        $response = $controller->updateCloningAction();
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

	public function testSetPointsAction_NoUser()
	{
		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("findOneByUsername"))
			->getMock();
		$repo->expects($this->once())->method('findOneByUsername')->with($this->equalTo("idontexist"))->will($this->returnValue(null));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->setPointsAction("idontexist", 50);
		$this->assertEquals($response->getContent(), '{"success":false}');
	}

	public function testEnabledAction()
	{
		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getSingleScalarResult"))
			->getMock();
		$query->expects($this->once())->method('getSingleScalarResult')->will($this->returnValue(5));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array('select',"where", "getQuery"))
			->getMock();
        $qb->expects($this->once())->method('select')->with($this->equalTo('count(u.id)'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.enabled = 1'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->enabledAction();
		$this->assertEquals($response->getContent(), 5);
	}

	public function testActiveAction_NoneActive()
	{
		$users = array();
		for ($i = 0; $i < 5; $i++)
		{
			$users[] = new User();
		}
		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.enabled = 1'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->activeAction();
		$this->assertEquals($response->getContent(), 0);
	}

	public function testActiveAction_TwoOfFiveActive()
	{
		$users = array();
		for ($i = 0; $i < 5; $i++)
		{
			$users[] = new User();
		}

		$users[0]->setLastLogin(new \DateTime);
		$users[1]->setLastLogin(new \DateTime);

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("where", "getQuery"))
			->getMock();
		$qb->expects($this->once())->method('where')->with($this->equalTo('u.enabled = 1'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$controller = $this->setUpController($templating, $security, $em, $container);

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->activeAction();
		$this->assertEquals($response->getContent(), 2);
	}

	public function testInlineRegisterAction_NullArgs()
	{
		$form = $this->getMockBuilder('Symfony\Component\Form\Form')
			->disableOriginalConstructor()
			->setMethods(array("createView"))
			->getMock();
		$form->expects($this->once())->method("createView")->will($this->returnValue(null));

		$formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
			->disableOriginalConstructor()
			->setMethods(array("generateReferrals"))
			->getMock();
		$formHandler->expects($this->once())->method("generateReferrals")->with($this->equalTo(null), $this->equalTo(null))->will($this->returnValue($form));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

		$container->expects($this->once())->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));

		$templating->expects($this->once())->method("render")->will($this->returnValue("this is the registration form view"));

		$response = $controller->inlineRegisterAction();
		$this->assertEquals($response->getContent(), "this is the registration form view");
	}

	public function testInlineRegisterAction_WithArguments()
	{
		$form = $this->getMockBuilder('Symfony\Component\Form\Form')
			->disableOriginalConstructor()
			->setMethods(array("createView"))
			->getMock();
		$form->expects($this->once())->method("createView")->will($this->returnValue(null));

		$formHandler = $this->getMockBuilder('Codebender\UserBundle\Form\Handler\RegistrationFormHandler')
			->disableOriginalConstructor()
			->setMethods(array("generateReferrals"))
			->getMock();
		$formHandler->expects($this->once())->method("generateReferrals")->with($this->equalTo("bender"), $this->equalTo("abcdefg"))->will($this->returnValue($form));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getCurrentUserAction"), array($templating, $security, $em, $container));

		$container->expects($this->once())->method('get')->with($this->equalTo('fos_user.registration.form.handler'))->will($this->returnValue($formHandler));

		$templating->expects($this->once())->method("render")->will($this->returnValue("this is the registration form view"));

		$response = $controller->inlineRegisterAction("bender", "abcdefg");
		$this->assertEquals($response->getContent(), "this is the registration form view");
	}

	private function initArguments(&$templating, &$security, &$em, &$container)
	{
		$em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
			->disableOriginalConstructor()
			->getMock();

		$templating = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
			->disableOriginalConstructor()
			->getMock();

		$security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
			->disableOriginalConstructor()
			->getMock();

		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
	}

	public function testGetTopUsersAction()
	{
		$users = array();
		for ($i = 0; $i < 5; $i++)
		{
			$user = new User();
			$user->setKarma($i*10);
			$user->setUsername("iamfake");
			$users[] = $user;
		}

		$query = $this->getMockBuilder('MyQuery')
			->disableOriginalConstructor()
			->setMethods(array("getResult"))
			->getMock();
		$query->expects($this->once())->method('getResult')->will($this->returnValue($users));

		$qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
			->disableOriginalConstructor()
			->setMethods(array("orderBy","setMaxResults", "getQuery"))
			->getMock();
		$qb->expects($this->once())->method('orderBy')->with($this->equalTo('u.karma'), $this->equalTo('DESC'))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('setMaxResults')->with($this->equalTo(5))->will($this->returnValue($qb));
		$qb->expects($this->once())->method('getQuery')->will($this->returnValue($query));

		$repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
			->disableOriginalConstructor()
			->setMethods(array("createQueryBuilder"))
			->getMock();
		$repo->expects($this->once())->method('createQueryBuilder')->with($this->equalTo('u'))->will($this->returnValue($qb));

		$this->initArguments($templating, $security, $em, $container);
		$controller = $this->getMock("Codebender\UserBundle\Controller\DefaultController", array("getUserAction"), array($templating, $security, $em, $container));
		$controller->expects($this->exactly(5))->method('getUserAction')->with($this->equalTo("iamfake"))->will($this->returnValue(new Response('{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}')));

		$em->expects($this->once())->method('getRepository')->with($this->equalTo('CodebenderUserBundle:User'))->will($this->returnValue($repo));

		$response = $controller->getTopUsersAction(5);
		$this->assertEquals($response->getContent(), '{"success":true,"list":[{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0},{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0},{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0},{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0},{"success":true,"id":1,"email":"a@fake.email","username":"iamfake","firstname":"fake","lastname":"basterd","twitter":"atwitteraccount","karma":150,"points":150,"referrals":5,"referrer_username":null,"referral_code":null,"walkthrough_status":0}]}');
	}

	private function setUpController(&$templating, &$security, &$em, &$container)
		{
			$this->initArguments($templating, $security, $em, $container);
			$controller = new DefaultController($templating, $security, $em, $container);
			return $controller;
		}
}

class MyQuery extends AbstractQuery
{
	public function getSQL(){}

	public function _doExecute(){}
	public function getResult()
	{
		return "hello";
	}
}
