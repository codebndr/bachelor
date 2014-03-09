<?php

namespace Codebender\BoardBundle\Controller;

use Codebender\BoardBundle\Entity\Board;
use Codebender\BoardBundle\Entity\PersonalBoards;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultController extends Controller
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    protected $sc;

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Creates Personal Board
     * 
     * @param Integer $owner
     * @param String $description
     * @param DateTime $starts
     * @param DateTime $expires
     * @param Integer $number
     * @return JSON encoded Response
     */
    public function createBoardsPlanAction($owner, $description, $starts, $expires, $number)
    {
        $plan = new PersonalBoards();

        $plan->setOwner($this->em->getRepository('CodebenderUserBundle:User')->find($owner));
        $plan->setDescription($description);
        $plan->setStarts($starts);
        $plan->setExpires($expires);
        $plan->setNumber($number);

        $this->em->persist($plan);
        $this->em->flush();

        $response = new Response(json_encode(array("success" => true, "id" => $plan->getId())));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Lists boards
     * 
     * @return JSON encoded Response
     */
	public function listAction()
	{
		$boards = array();

        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        if ($user['success'])
        {
            $db_boards = $this->em->getRepository('CodebenderBoardBundle:Board')->findByOwner($user["id"]);

            foreach ($db_boards as $board)
            {
                $boards[] = array(
                    "name" => $board->getName(),
                    "upload" => json_decode($board->getUpload(), true),
                    "bootloader" => json_decode($board->getBootloader(), true),
                    "build" => json_decode($board->getBuild(), true),
                    "description" => $board->getDescription(),
                    "personal" => true,
                    "id" => $board->getId()
                );
            }
        }

		$db_boards = $this->em->getRepository('CodebenderBoardBundle:Board')->findBy(array("owner" => null));

		foreach ($db_boards as $board)
		{
			$boards[] = array(
				"name" => $board->getName(),
				"upload" => json_decode($board->getUpload(), true),
				"bootloader" => json_decode($board->getBootloader(), true),
				"build" => json_decode($board->getBuild(), true),
				"description" => $board->getDescription(),
				"personal" => false,
                "id" => $board->getId()
			);
		}

        $response = new Response(json_encode($boards));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

		return $response;
	}

    /**
     * Edits a Boards name and description 
     *
     * @param Integer $id
     * @param String $name
     * @param String $description
     * @return JSON encoded Response
     */
    public function editAction($id, $name, $description)
    {
        $board = $this->getBoardById($id);
        $perm = json_decode($this->checkBoardPermissions($id), true);
        if(!$perm['success'])
        {
            $response = new Response(json_encode(array("success" => false, "message" => "Cannot edit board '".$board->getName()."'.")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $board->setName($name);
        $board->setDescription($description);

        $em = $this->em;
        $em->persist($board);
        $em->flush();

        $response = new Response(json_encode(array("success" => true, "new_name" => $name, "new_desc" => $description)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Sets name of Board 
     *
     * @param Integer $id
     * @param String $description
     * @return JSON encoded Response
     */
    public function setNameAction($id, $name)
    {
        $board = $this->getBoardById($id);
        $perm = json_decode($this->checkBoardPermissions($id), true);
        if(!$perm['success'])
        {
            $response = new Response(json_encode(array("success" => false, "message" => "Cannot set name for board '".$board->getName()."'.")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $board->setName($name);
        $em = $this->em;
        $em->persist($board);
        $em->flush();

        $response = new Response(json_encode(array("success" => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Sets description of Board
     *
     * @param Integer $id
     * @param String $description
     * @return JSON encoded Response
     */
    public function setDescriptionAction($id, $description)
    {
        $board = $this->getBoardById($id);
        $perm = json_decode($this->checkBoardPermissions($id), true);
        if(!$perm['success'])
        {
            $response = new Response(json_encode(array("success" => false, "message" => "Cannot set description for board '".$board->getName()."'.")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        $board->setDescription($description);
        $em = $this->em;
        $em->persist($board);
        $em->flush();

        $response = new Response(json_encode(array("success" => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Adds a Board given a User and Board Array 
     *
     * @param Array $b
     * @param Integer $user_id
     * @return JSON encoded Response
     */
    public function addBoardAction($b, $user_id)
    {
        $owner = $this->em->getRepository('CodebenderUserBundle:User')->find($user_id);

        $board = new Board();
        $board->setName($b["name"]);
        $board->setUpload(json_encode($b["upload"]));
        $board->setBootloader(json_encode($b["bootloader"]));
        $board->setBuild(json_encode($b["build"]));
        $board->setOwner($owner);
        $board->setDescription("Personal Board");

        $this->em->persist($board);
        $this->em->flush();

        $response = new Response(json_encode(array("success" => true)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * Checks if Board is Valid 
     *
     * @param Array $b
     * @return JSON encoded Response
     */
    public function isValidBoardAction($b)
    {
        if(isset($b['name']) && isset($b["upload"]) && isset($b["bootloader"]) && isset($b["build"]))
        {
            $success = true;
        }
        else
        {
            $success = false;
        }

        $response = new Response(json_encode(array("success" => $success)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Deletes a Board 
     * 
     * @param Integer $id
     * @return JSON encoded Response
     */
    public function deleteBoardAction($id)
    {
        $perm = json_decode($this->checkBoardPermissions($id), true);

        if(!($perm['success']))
        {
            $response = new Response(json_encode(array("success" => false, "message" => "You have no permissions to delete this board.")));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $board = $this->getBoardById($id);

        $this->em->remove($board);
        $this->em->flush();

        $response = new Response(json_encode(array("success" => true, "message" => "Board '".$board->getName()."' was successfully deleted.")));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * Checks if user can add Personal Boards
     *
     * @param Integer $user_id
     * @return JSON encoded Response
     */
    public function canAddPersonalBoardAction($user_id)
    {
        $response = new Response($this->canAddPersonalBoard($user_id));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Parses properties of Boards
     *
     * @param String $txtProperties
     * @return JSON encoded Response board properties
     */
    public function parsePropertiesFileAction($txtProperties) {
        $result = array();
        $lines = explode("\n", $txtProperties);
        $key = "";
        $isWaitingOtherLine = false;
        foreach ($lines as $i => $line) {
            if (empty($line) || (!$isWaitingOtherLine && strpos($line, "#") === 0))
                continue;

            if (!$isWaitingOtherLine) {
                $key = substr($line, 0, strpos($line, '='));
                $value = substr($line, strpos($line, '=')+1, strlen($line));
            }
            else {
                $value .= $line;
            }

            /* Check if ends with single '\' */
            if (strrpos($value, "\\") === strlen($value)-strlen("\\")) {
                $value = substr($value,0,strlen($value)-1)."\n";
                $isWaitingOtherLine = true;
            }
            else {
                $isWaitingOtherLine = false;
            }

            $keys = explode(".", $key);
            $local_result = $value;
            for($i = count($keys) -1; $i >= 0; $i--)
            {
                $local_result = array($keys[$i] => $local_result);
            }

            $result = array_merge_recursive($result, $local_result);
            unset($lines[$i]);
        }

        $response = new Response(json_encode(array("success" => true, "boards" => $result)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Checks if user can add Personal Boards
     *
     * @param Integer $user_id
     * @return JSON encoded permission level
     */
    protected function canAddPersonalBoard($user_id)
    {
        $boards = $this->em->getRepository('CodebenderBoardBundle:Board')->findByOwner($user_id);
        $currentPersonal = count($boards);


        $prs= $this->em->getRepository('CodebenderBoardBundle:PersonalBoards')->findByOwner($user_id);
        $maxPersonal = 0;
        foreach ($prs as $p)
        {
            $now = new \DateTime("now");
            if($now>= $p->getStarts() && ($p->getExpires()==NULL || $now < $p->getExpires()))
                $maxPersonal+=$p->getNumber();
        }

        if($currentPersonal >= $maxPersonal)
            return json_encode(array("success" => false, "error" => "Cannot add personal board."));
        else
            return json_encode(array("success" => true, "available" => $maxPersonal - $currentPersonal));
    }

    /**
     * Checks Board Permissions based on an ID 
     *
     * @param Integer $id
     * @return JSON encoded permission level
     */
    protected function checkBoardPermissions($id)
    {
        $board = $this->getBoardById($id);
        $current_user = $this->sc->getToken()->getUser();

        if($board->getOwner()!== null && $current_user !== "anon." && $current_user->getId() === $board->getOwner()->getId())
            return json_encode(array("success" => true));
        else
            return json_encode(array("success" => false, "error" => "You have no permissions for this board.", "id" => $id));
    }

    /**
     * Gets a board from DB based on its ID
     *
     * @param Integer $id
     * @return Board Entity
     */
    protected function getBoardById($id)
    {
        $em = $this->em;
        $board = $this->em->getRepository('CodebenderBoardBundle:Board')->find($id);
        if (!$board)
            throw $this->createNotFoundException('No board found with id '.$id);

        return $board;
    }

    /**
     * Construction of Controller
     *
     * @param EntityManager $entityManager
     * @param SecurityContext $securityContext
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->sc = $securityContext;
        $this->container = $container;
    }
}
