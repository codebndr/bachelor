<?php

namespace Codebender\UserBundle\Controller;

use Doctrine\ORM\EntityManager;
use Codebender\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo Controller requires cleanup for Search methods as they are not needed in Bachelor
 */
class DefaultController extends Controller
{
	protected $templating;
	protected $sc;
	protected $em;
	protected $container;

	/**
	 * Checks if given Username exists
	 *
	 * @param String $username
	 * @return True or False
	 */
	public function existsAction($username)
	{
		$response = json_decode($this->getUserAction($username)->getContent(), true);
		if($response["success"])
			return new Response("true");
		else
			return new Response("false");
	}

	/**
	 * Checks if given Email exists
	 *
	 * @param String $email
	 * @return True or False
	 */
	public function emailExistsAction($email)
	{
		/**
		 * @todo Fix this to use a generic function, not call the db directly
		 */
		$user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByEmail($email);
		if($user)
			return new Response("true");
		else
			return new Response("false");
	}

	/**
	 * Gets a user based on Username
	 *
	 * @param String $username
	 * @return JSON encoded User array
	 */
	public function getUserAction($username)
	{
		$response = array("success" => false);
		/** @var User $user */
		$user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($username);
		if ($user)
		{
			$response = array("success" => true,
			"id" => $user->getId(),
			"email" => $user->getEmail(),
			"username" => $user->getUsername(),
			"firstname" => $user->getFirstname(),
			"lastname" => $user->getLastname(),
			"twitter" => $user->getTwitter(),
			"karma" => $user->getKarma(),
			"points" => $user->getPoints(),
			"referrals" => $user->getReferrals(),
			"referrer_username" => $user->getReferrerUsername(),
			"referral_code" => $user->getReferralCode(),
			"walkthrough_status" => $user->getWalkthroughStatus(),
			"last_walkthrough_date" => $user->getLastWalkthroughDate(),
			"preregistration_date" => $user->getPreregistrationDate(),
			"registration_date" => $user->getRegistrationDate(),
			"actual_last_login" => $user->getActualLastLogin(),
			"edit_count" => $user->getEditCount(),
			"last_edit" => $user->getLastEdit(),
			"compile_count" => $user->getCompileCount(),
			"last_compile" => $user->getLastCompile(),
			"flash_count" => $user->getFlashCount(),
			"last_flash" => $user->getLastFlash(),
			"cloning_count" => $user->getCloningCount(),
			"last_cloning" => $user->getLastCloning(),
			"eula" => $user->getEula()
			);
		}
		return new Response(json_encode($response));
	}

	/**
	 * Gets the current authenticated user
	 *
	 * @return JSON encoded User array
	 */
	public function getCurrentUserAction()
	{
		$current_user = $this->sc->getToken()->getUser();
		if($current_user !== "anon.")
		{
			$name = $current_user->getUsername();
			$data = json_decode($this->getUserAction($name)->getContent(), true);
			if ($data["success"] === false)
			{
				throw $this->createNotFoundException('No user found with username '.$name);
			}
			/** @var User $current_user */

			$current_user->setActualLastLogin(new \DateTime());
			$this->em->flush();
			$response = $data;
		}
		else
		{
			$response = array("success" => false);
		}
		return new Response(json_encode($response));

	}

	/**
	 * Searches for User based on a given token
	 *
	 * @param String $token
	 * @return JSON encoded search results
	 */
	public function searchAction($token)
	{
		$results_name = json_decode($this->searchNameAction($token)->getContent(), true);
		$results_uname = json_decode($this->searchUsernameAction($token)->getContent(), true);
		$results_twit = json_decode($this->searchTwitterAction($token)->getContent(), true);
		$results = $results_name + $results_uname + $results_twit;
		return new Response(json_encode($results));
	}

	/**
	 * Searches Users for user with a name
	 *
	 * @param String $token
	 * @return JSON encoded search results
	 */
	public function searchNameAction($token)
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.firstname LIKE :name OR u.lastname LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->setMaxResults(1000)->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[$user->getId()] = array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername(), "karma" => $user->getKarma());
		}
		return new Response(json_encode($result));
	}

	/**
	 * Searches Users for user with a username
	 *
	 * @param String $token
	 * @return JSON encoded search results
	 */
	public function searchUsernameAction($token)
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.username LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->setMaxResults(1000)->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[$user->getId()] = array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername(), "karma" => $user->getKarma());
		}
		return new Response(json_encode($result));
	}

	/**
	 * Searches Users for user with twitter name
	 *
	 * @param String $token
	 * @return JSON encoded search results
	 */
	public function searchTwitterAction($token)
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.twitter LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->setMaxResults(1000)->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[$user->getId()] = array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername(), "karma" => $user->getKarma());
		}
		return new Response(json_encode($result));
	}

	/**
	 * Sets referrer on a user
	 *
	 * @param String $username
	 * @param String $referrer_username
	 * @return JSON encoded success of failure
	 */
	public function setReferrerAction($username, $referrer_username)
	{

		/** @var User $user */
		$user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($username);
		if ($user != NULL)
		{
			/** @var User $referrer */
			$referrer = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($referrer_username);

			if($referrer != NULL)
			{
				$referrer->setReferrals($referrer->getReferrals() + 1);
				$referrer->setKarma($referrer->getKarma() + 20);
				$referrer->setPoints($referrer->getPoints() + 20);
				$user->setReferrer($referrer);
				$this->em->flush();
				return new Response(json_encode(array("success" => true)));
			}
			return new Response(json_encode(array("success" => false)));

		}

		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Sets karma on given user
	 *
	 * @param String $username
	 * @param Integer $karma
	 * @return JSON encoded success or failure
	 */
	public function setKarmaAction($username, $karma)
	{
		$user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($username);

		if ($user != NULL)
		{
			$user->setKarma(intval($karma));
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Sets points on given user
	 *
	 * @param String $username
	 * @param Integer $points
	 * @return JSON encoded success or failure
	 */
	public function setPointsAction($username, $points)
	{
		$user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($username);

		if($user != NULL)
		{
			$user->setPoints(intval($points));
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Sets walkthrough status
	 *
	 * @param Integer $status
	 * @return JSON encoded success or failure
	 */
	public function setWalkthroughStatusAction($status)
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setLastWalkthroughDate(new \DateTime());
			if ($current_user->getWalkthroughStatus() < $status)
			{
				if ($status == 5)
					$current_user->setPoints($current_user->getPoints() + 50);
				$current_user->setWalkthroughStatus($status);
			}
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Sets EULA
	 *
	 * @return JSON encoded success or failure
	 */
	public function setEulaAction()
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if ($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setEula(true);
			$this->em->flush();

			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Updates Edit Count on User
	 *
	 * @return JSON encoded success or failure
	 */
	public function updateEditAction()
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if ($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setEditCount($current_user->getEditCount() +1);
			$current_user->setLastEdit(new \DateTime());
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Updates compile count on User
	 *
	 * @return JSON encoded success or failure
	 */
	public function updateCompileAction()
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if ($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setCompileCount($current_user->getCompileCount() + 1);
			$current_user->setLastCompile(new \DateTime());
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Updates flash count on user
	 *
	 * @return JSON encoded success or failure
	 */
	public function updateFlashAction()
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if ($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setFlashCount($current_user->getFlashCount() + 1);
			$current_user->setLastFlash(new \DateTime());
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Updates cloning count on user
	 *
	 * @return JSON encoded success or failure
	 */
	public function updateCloningAction()
	{
		$response = json_decode($this->getCurrentUserAction()->getContent(), true);
		if ($response["success"] === true)
		{
			/** @var User $current_user */
			$current_user = $this->em->getRepository('CodebenderUserBundle:User')->findOneByUsername($response["username"]);
			$current_user->setCloningCount($current_user->getCloningCount() + 1);
			$current_user->setLastCloning(new \DateTime());
			$this->em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		return new Response(json_encode(array("success" => false)));
	}

	/**
	 * Gives a count of enabled users
	 *
	 * @return Integer number of enabled users
	 */
	public function enabledAction()
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$count = $repository->createQueryBuilder('u')->select('count(u.id)')->where('u.enabled = 1')->getQuery()->getSingleScalarResult();
		return new Response($count);

	}

	/**
	 * Gets amount of users that are active
	 *
	 * @return Integer number of active users
	 */
	public function activeAction()
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$users = $repository->createQueryBuilder('u')->where('u.enabled = 1')->getQuery()->getResult();
		$dayofyear = new \DateTime;
		$count = 0;
		foreach($users as $user)
		{
			if($user->getLastLogin() != null)
			{
				if($dayofyear->format("z") == $user->getLastLogin()->format("z"))
					$count++;
			}
		}
		return new Response($count);

	}

	/**
	 * Renders Inline Registration
	 * 
	 * @param String $referrer
	 * @param Integer $referral_code
	 * @return Rendered Inline Registration Template
	 */
	public function inlineRegisterAction($referrer=null, $referral_code=null)
	{


		/** @var \Codebender\UserBundle\Form\Handler\RegistrationFormHandler $formHandler */
		$formHandler = $this->container->get('fos_user.registration.form.handler');
		$form = $formHandler->generateReferrals($referrer, $referral_code);

		return new Response($this->templating->render('CodebenderUserBundle:Registration:register_inline.html.twig', array(
	            'form' => $form->createView()
//        ,
//	            'theme' => $this->container->getParameter('fos_user.template.theme'),
	        )));
	}

	/**
	 * Gets users with top Karma
	 *
	 * @param Integer $count
	 * @return JSON encoded array with $count many users
	 */
	public function getTopUsersAction($count)
	{
		$repository = $this->em->getRepository('CodebenderUserBundle:User');
		$users = $repository->createQueryBuilder('u')
			->orderBy('u.karma', 'DESC')
			->setMaxResults($count)
			->getQuery()->getResult();

		$users_array = array();
		foreach($users as $user)
		{
			$users_array[] = json_decode($this->getUserAction($user->getUsername())->getContent(), true);
		}

		return new Response(json_encode(array("success" => true, "list" => $users_array)));
	}

	/**
	 * Constructor
	 * 
	 * @param Symfony\Component\Templating\EngineInterface $templating
	 * @param Symfony\Component\Security\Core\SecurityContext $securityContext
	 * @param Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EngineInterface $templating, SecurityContext $securityContext, EntityManager $entityManager, ContainerInterface $container)
	{
		$this->templating = $templating;
		$this->sc = $securityContext;
	    $this->em = $entityManager;
		$this->container = $container;
	}

}
