<?php

namespace Codebender\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Regex;
use Codebender\UtilitiesBundle\Handler\DefaultHandler;
use Codebender\ProjectBundle\Controller\SketchController;
use Codebender\UtilitiesBundle\Entity\ViewLog;


class DefaultController extends Controller
{

	public function indexAction()
	{
        $session = $this->container->get('session');

		if ($this->get('security.context')->isGranted('ROLE_USER'))
		{
			// Load user content here
            $usercontroller = $this->get('codebender_user.usercontroller');
			$user = json_decode($usercontroller->getCurrentUserAction()->getContent(), true);
			{

				//TODO: Test this code!
				$projectmanager = $this->get('codebender_project.sketchmanager');
				$priv_proj_avail = json_decode($projectmanager->canCreatePrivateProjectAction($user["id"])->getContent(), true);
				if(!$priv_proj_avail["success"])
				{
					$priv_proj_avail["available"] = 0;
				}
                $this->get('codebender_utilities.logcontroller')->logViewAction($user["id"], ViewLog::HOME_PAGE_VIEW, 'HOME_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());
				return $this->render('CodebenderGenericBundle:Index:list.html.twig', array('user' => $user, "avail_priv_proj" => $priv_proj_avail));
			}
		}
        $this->get('codebender_utilities.logcontroller')->logViewAction(null, ViewLog::HOME_PAGE_VIEW, 'HOME_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());
		return $this->render('CodebenderGenericBundle:Index:index.html.twig');
	}

	public function userAction($user)
	{
		$json_output = false;
		if(strripos($user, ".json") !== false && strripos($user, ".json") == strlen($user) -5)
		{
			$user = substr($user, 0, strlen($user) -5);
			$json_output = true;
		}

		$user = json_decode($this->get('codebender_user.usercontroller')->getUserAction($user)->getContent(), true);

		if ($user["success"] === false)
		{
			if($json_output)
			{
				return new Response("{\"success\":false,\"error\":\"There is no such user!\"}");
			}
			else
				return $this->render('CodebenderGenericBundle:Default:minor_error.html.twig', array('error' => "There is no such user."));
		}

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$projects = $projectmanager->listAction($user["id"])->getContent();
		$projects = json_decode($projects, true);
        if($json_output)
        {
            foreach($projects as $key => $project)
            {
                unset($projects[$key]["is_public"]);
                $projects[$key]["url"] = $this->generateUrl("CodebenderGenericBundle_project", array("id" => $project["id"]), true);
                $projects[$key]["json_url"] = $this->generateUrl("CodebenderGenericBundle_json_project", array("id" => $project["id"]), true);
            }
            $response = array("success" => true, "username" => $user["username"], "name" => $user["firstname"]." ".$user["lastname"], "projects" => $projects);
            return new Response(json_encode($response));
        }

		$utilities = $this->get('codebender_utilities.handler');

		$result = json_decode($utilities->get("http://api.twitter.com/1/statuses/user_timeline/".$user["twitter"].".json"), true);
		if (!isset($result["errors"]))
		{
			$lastTweet = $result[0]["text"]; // show latest tweet
		}
		else
		{
			$lastTweet = false;
		}
		$image = $utilities->get_gravatar($user["email"], 120);
		return $this->render('CodebenderGenericBundle:Default:user.html.twig', array('user' => $user, 'projects' => $projects, 'lastTweet' => $lastTweet, 'image' => $image));
	}

	public function projectAction($id, $embed = false, $json_output = false)
	{
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
		/** @var SketchController $projectmanager */
		$projectmanager = $this->get('codebender_project.sketchmanager');
		$projects = NULL;

		$project = json_decode($projectmanager->checkExistsAction($id)->getContent(), true);
		if ($project["success"] === false)
		{
			if ($json_output)
			{
				$json = json_encode(array("success" => false, 'error' => "There is no such project!"));
				return new Response($json, 200, array("Content-Type" => "text/json"));
			}
			return $this->render('CodebenderGenericBundle:Default:minor_error.html.twig', array('error' => "There is no such project!"));
		}

		$permissions = json_decode($projectmanager->checkWriteProjectPermissionsAction($id)->getContent(), true);
		if ($permissions["success"] && !$embed && !$json_output)
		{
            $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::EDITOR_PROJECT_VIEW, 'EDITOR_PROJECT_VIEW', json_encode(array("project" => $id)),$session->getId(), $this->getRequest()->hasPreviousSession());
			return $this->forward('CodebenderGenericBundle:Editor:edit', array("id" => $id));
		}

		$permissions = json_decode($projectmanager->checkReadProjectPermissionsAction($id)->getContent(), true);
		if (!$permissions["success"])
		{
			if ($json_output)
			{
				$json = json_encode(array("success" => false, 'error' => "There is no such project!"));
				return new Response($json, 200, array("Content-Type" => "text/json"));
			}
			return $this->render('CodebenderGenericBundle:Default:minor_error.html.twig', array('error' => "There is no such project!"));
		}

		$owner = $projectmanager->getOwnerAction($id)->getContent();
		$owner = json_decode($owner, true);
		$owner = $owner["response"];

		$name = $projectmanager->getNameAction($id)->getContent();
		$name = json_decode($name, true);
		$name = $name["response"];

		$parent = $projectmanager->getParentAction($id)->getContent();
		$parent = json_decode($parent, true);
		if ($parent["success"])
		{
			$parent = $parent["response"];
		}
		else
			$parent = NULL;

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		if ($files["success"])
		{
			$files = $files["list"];
			foreach ($files as $key => $file)
			{
				$files[$key]["code"] = htmlspecialchars($file["code"]);
			}

            $router = $this->get('router');
			$json = array("project" => array("name" => $name, "url" => $router->generate('CodebenderGenericBundle_project', array("id" => $id), true)), "user" => array("name" => $owner["username"], "url" => $router->generate('CodebenderGenericBundle_user', array('user' => $owner['username']), true)), "clone_url" => $router->generate('CodebenderUtilitiesBundle_clone', array('id' => $id), true), "download_url" => $router->generate('CodebenderUtilitiesBundle_download', array('id' => $id), true), "files" => $files);
			$json = json_encode($json);

			if ($embed)
            {
                $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::EMBEDDED_PROJECT_VIEW, 'EMBEDDED_PROJECT_VIEW', json_encode(array("project" => $id)),$session->getId(), $this->getRequest()->hasPreviousSession());
				return $this->render('CodebenderGenericBundle:Default:project_embeddable.html.twig', array("json" => $json));
            }
			if($json_output)
			{
                $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::JSON_PROJECT_VIEW, 'JSON_PROJECT_VIEW', json_encode(array("project" => $id)),$session->getId(), $this->getRequest()->hasPreviousSession());
				$json = json_decode($json, true);
				$json["success"] = true;
				$json = json_encode($json);
				return new Response($json, 200, array("Content-Type" => "text/json"));
			}
            $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::PROJECT_VIEW, 'PROJECT_VIEW', json_encode(array("project" => $id)),$session->getId(), $this->getRequest()->hasPreviousSession());
			return $this->render('CodebenderGenericBundle:Default:project.html.twig', array('project_name' => $name, 'owner' => $owner, 'files' => $files, "project_id" => $id, "parent" => $parent, "json" => $json));
		}
    // @codeCoverageIgnoreStart
	}
    // @codeCoverageIgnoreEnd
	public function projectfilesAction()
	{
		$id = $this->getRequest()->request->get('project_id');

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$projects = NULL;

		$project = json_decode($projectmanager->checkExistsAction($id)->getContent(), true);
		if ($project["success"] === false)
		{
            $response = new Response("Project Not Found", 404);
            $response->headers->set('Access-Control-Allow-Origin', '*');

            return $response;
		}

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		$files = $files["list"];

		$files_hashmap = array();
		foreach ($files as $file)
		{
			$files_hashmap[$file["filename"]] = htmlspecialchars($file["code"]);
		}
        $response = new Response(json_encode($files_hashmap));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        
		return $response;
	}

	public function librariesAction()
	{
		$utilities = $this->get('codebender_utilities.handler');

		$libraries = json_decode($utilities->get($this->container->getParameter('library')), true);
        if($libraries == NULL || $libraries['success'] == false)
            return $this->render('CodebenderGenericBundle:Default:minor_error.html.twig', array('error' => "Sorry! The library list could not be fetched."));
		$categories = $libraries["categories"];

        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::LIBRARY_PAGE_VIEW, 'LIBRARY_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());

		return $this->render('CodebenderGenericBundle:Default:libraries.html.twig', array('categories' => $categories));
	}

	public function exampleAction($library, $example, $embed = false)
	{
		$utilities = $this->get('codebender_utilities.handler');
		$response = json_decode($utilities->get($this->container->getParameter('library')."/get/".$library."/".$example ), true);
        if($response == NULL || $response['success'] == false)
            return $this->render('CodebenderGenericBundle:Default:minor_error.html.twig', array('error' => "Sorry! The library list could not be fetched."));
        $files = array();
        foreach($response['files'] as $f)
        {
            $files[] = array("filename" => $f['filename'], "code" => htmlspecialchars($f['code']));
        }
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $example_parts = explode(":",$example);
        $session = $this->container->get('session');
        $json =  array("project" => array("name" => $example_parts[count($example_parts) - 1], "url" => ""), "user" => array("name" => "", "url" => ""), "clone_url" => "", "download_url" => $this->get('router')->generate('CodebenderUtilitiesBundle_downloadexample', array('name' => $example, 'url' => "/get/".$library."/".$example ), true), "files" => $files);
        $json = json_encode($json);



        if($embed)
        {
            $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::EMBEDDED_LIBRARY_EXAMPLE_VIEW, 'EMBEDDED_LIBRARY_EXAMPLE_VIEW', json_encode(array("library" => $library, "example" => $example)),$session->getId(), $this->getRequest()->hasPreviousSession());
            return $this->render('CodebenderGenericBundle:Default:project_embeddable.html.twig', array('library' => $library, 'example' => $example_parts[count($example_parts) - 1], 'files' => $files, "type" => "example", "json" => $json));
        }
        else
        {
            $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::LIBRARY_EXAMPLE_VIEW, 'LIBRARY_EXAMPLE_VIEW', json_encode(array("library" => $library, "example" => $example)),$session->getId(), $this->getRequest()->hasPreviousSession());
            return $this->render('CodebenderGenericBundle:Default:example.html.twig', array('library' => $library, 'example' => $example_parts[count($example_parts) - 1], 'files' => $files, "type" => "example", 'json'=>$json));
        }

	}

	public function boardsAction()
	{
		$boardcontroller = $this->get('codebender_board.defaultcontroller');
		$boards = json_decode($boardcontroller->listAction()->getContent(), true);

		$available_boards = array("success" => false);
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
		if ($user['success'])
		{
			// Load user content here
			{
				$available_boards = json_decode($boardcontroller->canAddPersonalBoardAction($user["id"])->getContent(), true);
			}
		}
		if (!$available_boards["success"])
		{
			$available_boards["available"] = 0;
		}
        $session = $this->container->get('session');
        $this->get('codebender_utilities.logcontroller')->logViewAction($user['success']? $user["id"]:null , ViewLog::BOARDS_PAGE_VIEW, 'BOARDS_PAGE_VIEW', "",$session->getId(), $this->getRequest()->hasPreviousSession());
		return $this->render('CodebenderGenericBundle:Default:boards.html.twig', array('boards' => $boards, 'available_boards' => $available_boards));
	}


	public function embeddedCompilerFlasherJavascriptAction()
	{
		$response = $this->render('CodebenderGenericBundle:CompilerFlasher:compilerflasher.js.twig');
		$response->headers->set('Content-Type', 'text/javascript');

		return $response;
	}
}
