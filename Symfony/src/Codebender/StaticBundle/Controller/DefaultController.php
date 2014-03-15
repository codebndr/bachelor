<?php

namespace Codebender\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Codebender\StaticBundle\Entity\BlogPost;
//use Codebender\StaticBundle\Entity\Contact;
use Codebender\StaticBundle\Entity\Prereg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Codebender\UtilitiesBundle\Entity\ViewLog;


class developer
{
	public $name;
	public $image;
	public $description;

	/**
	 * Constructor
	 *
	 * @param String $name
	 * @param String $subtext
	 * @param String $image
	 * @param String $description
	 */
	function __construct($name, $subtext, $image, $description)
	{
		$this->name = $name;
		$this->subtext = $subtext;
		$this->image = $image;
		$this->description = $description;
	}
}

/**
 * @todo Much of this can be removed as it is no longer in use.
 */
class DefaultController extends Controller
{
	/**
	 * About page
	 *
	 * @return Twig Rendered About Template
	 */
	public function aboutAction()
	{
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::ABOUT_PAGE_VIEW, 'ABOUT_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());
		return $this->render('CodebenderStaticBundle:Default:about.html.twig');
	}

	/**
	 * Tech page
	 *
	 * @return Twig Rendered Tech Template
	 */
	public function techAction()
	{
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::TECH_PAGE_VIEW, 'TECH_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());
		return $this->render('CodebenderStaticBundle:Default:tech.html.twig');
	}

	/**
	 * Team page
	 *
	 * @return Twig Rendered Team Template
	 */
	public function teamAction()
	{

        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::TEAM_PAGE_VIEW, 'TEAM_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());

		$dev_images_dir = "images/developers/";
		$tzikis_name = "Vasilis Georgitzikis";
		$tzikis_title = "teh lead";
		$tzikis_avatar = $dev_images_dir . "tzikis.jpeg";
		$tzikis_desc = "I am a student at the Computer Engineering and Informatics Department of the University of Patras, Greece, a researcher at the Research Academic Computer Technology Institute, and an Arduino and iPhone/OSX/Cocoa developer. Basically, just a geek who likes building stuff, which is what started codebender in the first place.";
		$tzikis = new developer($tzikis_name, $tzikis_title, $tzikis_avatar, $tzikis_desc);

		$tsampas_name = "Stelios Tsampas";
		$tsampas_title = "teh crazor";
		$tsampas_avatar = $dev_images_dir . "tsampas.png";
		$tsampas_desc = "Yet another student at CEID. My task is to make sure to bring crazy ideas to the table and let others assess their value. I'm also responsible for the Arduino Ethernet TFTP bootloader, the only crazy idea that didn't originate from me. I also have a 'wierd' coding style that causes much distress to $tzikis_name.";
		$tsampas = new developer($tsampas_name, $tsampas_title, $tsampas_avatar, $tsampas_desc);

		$amaxilatis_name = "Dimitris Amaxilatis";
		$amaxilatis_title = "teh code monkey";
		$amaxilatis_avatar = $dev_images_dir . "amaxilatis.jpg";
		$amaxilatis_desc = "Master Student at the Computer Engineering and Informatics Department of the University of Patras, Greece. Researcher at  the Research Unit 1 of Computer Technology Institute & Press (Diophantus) in the fields of Distributed Systems and Wireless Sensor Networks.";
		$amaxilatis = new developer($amaxilatis_name, $amaxilatis_title, $amaxilatis_avatar, $amaxilatis_desc);

		$kousta_name = "Maria Kousta";
		$kousta_title = "teh lady";
		$kousta_avatar = $dev_images_dir . "kousta.png";
		$kousta_desc = "A CEID graduate. My task is to develop the various parts of the site besides the core 'code and compile' page that make it a truly social-building website.";
		$kousta = new developer($kousta_name, $kousta_title, $kousta_avatar, $kousta_desc);

		$orfanos_name = "Markellos Orfanos";
		$orfanos_title = "teh fireman";
		$orfanos_avatar = $dev_images_dir . "orfanos.jpg";
		$orfanos_desc = "I am also (not for long I hope) a student at the Computer Engineering & Informatics Department and probably the most important person in the team. My task? Make sure everyone keeps calm and the team is having fun. And yes, I'm the one who developed our wonderful options page. Apart from that, I'm trying to graduate and some time in the future to become a full blown Gentoo developer.";
		$orfanos = new developer($orfanos_name, $orfanos_title, $orfanos_avatar, $orfanos_desc);

		$dimakopoulos_name = "Dimitris Dimakopoulos";
		$dimakopoulos_title = "teh awesome";
		$dimakopoulos_avatar = $dev_images_dir . "dimakopoulos.jpg";
		$dimakopoulos_desc = "Student at the Computer Engineering and Informatics Department of the University of Patras, Greece, have worked as an intern for Philips Consumer Lifestyle in Eindhoven and for the Research Academic Computer Technology Institute in Patras. Totally excited with Codebender as it combines web development and distributed systems, them being among my favorite fields.";
		$dimakopoulos = new developer($dimakopoulos_name, $dimakopoulos_title, $dimakopoulos_avatar, $dimakopoulos_desc);

		$christidis_name = "Dimitrios Christidis";
		$christidis_title = "teh bald guy";
		$christidis_avatar = $dev_images_dir . "christidis.jpg";
		$christidis_desc = "Currently a student and an assistant administrator. I am responsible for the compiler backend, ensuring that it's fast and robust.  Known as a perfectionist, I often fuss over coding style and documentation.";
		$christidis = new developer($christidis_name, $christidis_title, $christidis_avatar, $christidis_desc);

		$baltas_name = "Alexandros Baltas";
		$baltas_title = "teh artist";
		$baltas_avatar = $dev_images_dir . "baltas.png";
		$baltas_desc = "I am a Computer Engineering and Infomatics Deparment graduate. And a drummer. When I'm not eating lots of food, I'm drinking lots of coffee and I can be found coding for codebender while distracting the rest of the team with my 'jokes'. Being a terrible designer, I mainly work on the backend.";
		$baltas = new developer($baltas_name, $baltas_title, $baltas_avatar, $baltas_desc);

        $papadopoulos_name = "Fotis Papadopoulos";
        $papadopoulos_title = "teh beard";
        $papadopoulos_avatar = $dev_images_dir . "papadopoulos.jpg";
        $papadopoulos_desc = "Call me Mr. Fotellos the Magnificent. Yet another CEID undergrad. I'm responsible for the backend, fixing the bugs that the rest of the team introduces in one of their drunken coding marathons. I also use Windows, but don't tell anyone.";
        $papadopoulos = new developer($papadopoulos_name, $papadopoulos_title, $papadopoulos_avatar, $papadopoulos_desc);

		$developers = array($tzikis, $baltas, $papadopoulos);
		$friends = array($amaxilatis, $orfanos, $tsampas, $christidis);
		$past = array($kousta, $dimakopoulos);
		return $this->render('CodebenderStaticBundle:Default:team.html.twig', array("developers" => $developers, "friends" => $friends, "past" => $past));
	}

	/**
	 * Tutorials page
	 *
	 * @return Twig Rendered Tutorials Template
	 */
	public function tutorialsAction()
	{
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::TUTORIALS_PAGE_VIEW, 'TUTORIALS_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());

		return $this->render('CodebenderStaticBundle:Default:tutorials.html.twig');
	}

	/**
	 * Walkthrough page
	 *
	 * @param Integer $page
	 * @return Twig Rendered Walkthrough Template
	 */
	public function walkthroughAction($page)
	{
		if (file_exists(__DIR__."/../Resources/views/Walkthrough/page".intval($page).".html.twig"))
		{
            $userController = $this->get('codebender_user.usercontroller');
            $user = json_decode($userController->getCurrentUserAction()->getContent(), true);
            $session = $this->container->get('session');
            $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::WALKTHROUGH_VIEW, 'WALKTHROUGH_VIEW', json_encode(array('page' => $page)),$session->getId(), $this->getRequest()->hasPreviousSession());
			$userController->setWalkthroughStatusAction(intval($page));
			return $this->render('CodebenderStaticBundle:Walkthrough:page'.intval($page).'.html.twig', array("page" => intval($page)));
		}
		else if($page == "download-complete")
		{
			return $this->render('CodebenderStaticBundle:Walkthrough:download-complete.html.twig');
		}

		return $this->redirect($this->generateUrl("CodebenderGenericBundle_index"));
	}

//	public function contactAction(Request $request)
//	{
//        // create a task and give it some dummy data for this example
//        $task = new Contact();
//		if ($this->get('security.context')->isGranted('ROLE_USER') === true)
//		{
//			$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
//	        $task->setName($user["firstname"]." ".$user["lastname"]." (".$user["username"].")");
//	        $task->setEmail($user["email"]);
//		}
//
//        $form = $this->createFormBuilder($task)
//            ->add('name', 'text')
//            ->add('email', 'email')
//            ->add('text', 'textarea')
//            ->getForm();
//
//		if ($request->getMethod() == 'POST')
//		{
//			$form->bindRequest($request);
//
//			if ($form->isValid())
//			{
//				$email_addr = $this->container->getParameter('email.addr');
//
//				// perform some action, such as saving the task to the database
//			    $message = \Swift_Message::newInstance()
//			        ->setSubject('codebender contact request')
//			        ->setFrom($email_addr)
//			        ->setTo($email_addr)
//			        ->setBody($this->renderView('CodebenderStaticBundle:Default:contact_email_form.txt.twig', array('task' => $task)))
//			    ;
//			    $this->get('mailer')->send($message);
//				$this->get('session')->setFlash('notice', 'Your message was sent!');
//
//				return $this->redirect($this->generateUrl('CodebenderStaticBundle_contact'));
//			}
//		}
//
//        return $this->render('CodebenderStaticBundle:Default:contact.html.twig', array(
//            'form' => $form->createView(),
//        ));
//	}

	/**
	 * Plugin page
	 *
	 * @return Twig Rendered Plugin Template
	 */
	public function pluginAction()
	{
		return $this->render('CodebenderStaticBundle:Default:plugin.html.twig', array());
	}

	/**
	 * Partner page
	 *
	 * @return Twig Rendered Partner Template
	 */
	public function partnerAction($name)
	{
		if(file_exists(__DIR__."/../Resources/views/Partner/".$name.".html.twig"))
			return $this->render('CodebenderStaticBundle:Partner:'.$name.'.html.twig');

		return $this->redirect($this->generateUrl("CodebenderGenericBundle_index"));
	}

	/**
	 * Info Points page
	 *
	 * @return Twig Rendered Info Points Template
	 */
	public function infoPointsAction()
	{
		return $this->render('CodebenderStaticBundle:Default:info_points.html.twig', array());
	}

	/**
	 * Info Karma page
	 *
	 * @return Twig Rendered Info Karma Template
	 */
	public function infoKarmaAction()
	{
		return $this->render('CodebenderStaticBundle:Default:info_karma.html.twig', array());
	}

	/**
	 * Info Private Projects page
	 *
	 * @return Twig Rendered Info Private Projects Template
	 */
	public function infoPrivateProjectsAction()
	{
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::PRIVATE_PROJECTS_INFO_VIEW, 'PRIVATE_PROJECTS_INFO_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());

		/** @var SketchController $projectmanager */
		$projectmanager = $this->get('codebender_project.sketchmanager');

		$records = json_decode($projectmanager->currentPrivateProjectRecordsAction()->getContent(), true);

		return $this->render('CodebenderStaticBundle:Default:info_private_projects.html.twig', array("records" => $records));
	}

	/**
	 * Upload Bootloader page
	 *
	 * @return Twig Rendered Upload Bootloader Template
	 */
	public function uploadBootloaderAction()
	{

        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::UPLOAD_BOOTLOADER_VIEW, 'UPLOAD_BOOTLOADER_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());

		$programmers = array();

		$programmers[] = array(
			"name" => "USBtinyISP",
			"communication" => "",
			"protocol" => "usbtiny",
			"speed" => "0",
			"force" => "false"
		);
		$programmers[] = array(
			"name" => "AVR ISP",
			"communication" => "serial",
			"protocol" => "stk500v1",
			"speed" => "0",
			"force" => "false"
		);
		$programmers[] = array(
			"name" => "AVRISP mkII",
			"communication" => "usb",
			"protocol" => "stk500v2",
			"speed" => "0",
			"force" => "false"
		);
		$programmers[] = array(
			"name" => "USBasp",
			"communication" => "usb",
			"protocol" => "usbasp",
			"speed" => "0",
			"force" => "false"
		);
		$programmers[] = array(
			"name" => "Parallel Programmer",
			"communication" => "dapa",
			"protocol" => "",
			"speed" => "0",
			"force" => "true"
		);
		$programmers[] = array(
			"name" => "Arduino as ISP",
			"communication" => "serial",
			"protocol" => "stk500v1",
			"speed" => "19200",
			"force" => "false"
		);

		return $this->render('CodebenderStaticBundle:Default:upload_bootloader.html.twig', array("programmers" => $programmers));
	}

	/**
	 * EULA page
	 *
	 * @return Twig Rendered EULA Template
	 */
	public function eulaAction()
	{
		//TODO: Log this?
		return $this->render('CodebenderStaticBundle:Default:eula.html.twig');
	}

}
