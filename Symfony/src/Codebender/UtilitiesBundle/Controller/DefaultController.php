<?php

namespace Codebender\UtilitiesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Codebender\UtilitiesBundle\Handler\DefaultHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Codebender\UtilitiesBundle\Handler\UploadHandler;
use ZipArchive;
use Codebender\UtilitiesBundle\Entity\Log;


class DefaultController extends Controller
{
    /**
     * Attempts to create a project
     * 
     * @return Redirects user to Generic Bundle Index or Project
     */
	public function newprojectAction()
	{
		syslog(LOG_INFO, "new project");

		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        if(!$user['success'])
        {
            return $this->redirect($this->generateUrl('CodebenderGenericBundle_index'));
        }

        $request = $this->getRequest()->request;

		$project_name = $request->get('project_name');
        $is_public = true;

        $request_is_public = $request->get('isPublic');
		if($request_is_public !== null)
		{
            $is_public = $request_is_public === 'true' ? true : false;
		}

		$text = "";
        $type = '';
        $code = $request->get('code');
		if ($code)
		{
			$files = json_decode($code, true);
                $notIno = array();
                foreach($files as $f)
                {
                    if ($f['filename'] == $project_name.".ino")
                        $text = htmlspecialchars_decode($f['code']);
                    else
                        $notIno[] = array("filename" => $f['filename'], "code" => htmlspecialchars_decode($f['code']));
                }

            $type = LOG::CLONE_LIB_EXAMPLE;
		}
		else
		{
			$utilities = $this->get('codebender_utilities.handler');
			$text = $utilities->default_text();
            $type = LOG::CREATE_PROJECT;
		}
        $projectController = $this->get('codebender_project.sketchmanager');

		$response = $projectController->createprojectAction($user["id"], $project_name, $text, $is_public)->getContent();
		$response=json_decode($response, true);
		if($response["success"])
		{
            if(isset($notIno) && count($notIno)>0)
            {
                foreach($notIno as $f)
                {
                    $projectController->createFileAction($response['id'], $f["filename"], $f["code"])->getContent();
                }
            }
            $this->get('codebender_utilities.logcontroller')->logAction($user["id"], $type, $type == LOG::CLONE_LIB_EXAMPLE ? 'CLONE_LIB_EXAMPLE' : 'CREATE_PROJECT', json_encode(array('success' => true, 'project' => $response['id'], 'is_public'=>$is_public)));
			return $this->redirect($this->generateUrl('CodebenderGenericBundle_project',array('id' => $response["id"])));
		}

		$this->get('session')->getFlashBag()->add('error', "Error: ".$response["error"]);
        $this->get('codebender_utilities.logcontroller')->logAction($user["id"], $type, $type == LOG::CLONE_LIB_EXAMPLE ? 'CLONE_LIB_EXAMPLE' : 'CREATE_PROJECT', json_encode(array('success' => false, 'is_public'=>$is_public, 'error'=>$response["error"])));
		return $this->redirect($this->generateUrl('CodebenderGenericBundle_index'));
	}

    /**
     * Deletes a Project
     *
     * @param Integer $id
     * @return Redirects user to GenericBundle Index
     */
	public function deleteprojectAction($id)
	{

		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->deleteAction($id)->getContent();
		$response=json_decode($response, true);
        if($response['success'])
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::DELETE_PROJECT, 'DELETE_PROJECT', json_encode(array('success' => true, 'project'=>$id)));
        else
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::DELETE_PROJECT, 'DELETE_PROJECT', json_encode(array('success' => false, 'project'=>$id)));

        return $this->redirect($this->generateUrl('CodebenderGenericBundle_index'));
	}

    /**
     * Lists Filenames in a Project
     *
     * @param Integer $id
     * @param Integer $show_ino
     * @return Twig rendered template of filenames list
     */
	public function listFilenamesAction($id, $show_ino)
	{
		$projectmanager = $this->get('codebender_project.sketchmanager');
		$files = $projectmanager->listFilesAction($id)->getContent();
		$files=json_decode($files, true);
		$files=$files["list"];

		if($show_ino == 0)
		{
			foreach($files as $key=>$file)
			if(strpos($file['filename'], ".ino") !== false)
			{
				unset($files[$key]);
			}
		}

		return $this->render('CodebenderUtilitiesBundle:Default:list_filenames.html.twig', array('files' => $files));
	}

    /**
     * Changes privacy of a project
     * 
     * @param Integer $id
     * @return JSON encoded success or failure
     */
	public function changePrivacyAction($id)
	{
		$projectmanager = $this->get('codebender_project.sketchmanager');
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$is_public = json_decode($projectmanager->getPrivacyAction($id)->getContent(), true);
		$is_public = $is_public["response"];

		if($is_public)
        {
			$response = $projectmanager->setProjectPrivateAction($id)->getContent();
            $json = json_decode($response,true);
            if($json['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_PERMISSIONS, 'CHANGE_PROJECT_PERMISSIONS', json_encode(array('success' => true, 'project'=>$id, 'from'=>'public', 'to'=>'private')));
            else
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_PERMISSIONS, 'CHANGE_PROJECT_PERMISSIONS', json_encode(array('success' => false, 'project'=>$id, 'from'=>'public', 'to'=>'private')));

        }
		else
        {
			$response = $projectmanager->setProjectPublicAction($id)->getContent();
            $json = json_decode($response,true);
            if($json['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_PERMISSIONS, 'CHANGE_PROJECT_PERMISSIONS', json_encode(array('success' => true, 'project'=>$id, 'from'=>'private', 'to'=>'public')));
            else
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_PERMISSIONS, 'CHANGE_PROJECT_PERMISSIONS', json_encode(array('success' => false, 'project'=>$id, 'from'=>'private', 'to'=>'public')));

        }

		return new Response($response);
	}

    /**
     * Renders project description
     *
     * @param Integer $id
     * @return Project Description or Not Found
     */
	public function renderDescriptionAction($id)
	{
		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->getDescriptionAction($id)->getContent();
		$response=json_decode($response, true);
		if($response["success"])
			return new Response($response["response"]);
		else
			return new Response("Project description not found.");
	}

    /**
     * Sets project Description
     *
     * @param Integer $id
     * @return JSON encoded success or failure
     */
	public function setDescriptionAction($id)
	{

		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$description = $this->getRequest()->request->get('data');

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->setDescriptionAction($id, $description)->getContent();
        $res = json_decode($response, true);
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_DESCRIPTION, 'CHANGE_PROJECT_DESCRIPTION', json_encode(array('success' => $res["success"], 'project' => $id)));
        return new Response(json_encode($res));
	}

    /**
     * Sets name of project
     *
     * @param Integer $id
     * @return Renamed project name
     */
	public function setNameAction($id)
	{

		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$new_name = $this->getRequest()->request->get('data');

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->renameAction($id, $new_name)->getContent();
        $res = json_decode($response, true);
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_PROJECT_NAME, 'CHANGE_PROJECT_NAME', json_encode(array('success' => $res["success"], 'project' => $id)));
		return new Response($response);
	}

    /**
     * Renames a file
     *
     * @param Integer $id
     * @return Renamed file name
     */
	public function renameFileAction($id)
	{

		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$old_filename = $this->getRequest()->request->get('oldFilename');
		$new_filename = $this->getRequest()->request->get('newFilename');

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->renameFileAction($id, $old_filename, $new_filename)->getContent();
        $res = json_decode($response, true);
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CHANGE_FILE_NAME, 'CHANGE_FILE_NAME', json_encode(array('success' => $res["success"], 'project' => $id, 'oldFilename' => $old_filename, 'newFilename' => $new_filename)));
		return new Response($response);
	}

    /**
     * Renders Sidebar
     *
     * @return Twig rendered sidebar template
     */
	public function sidebarAction()
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$files = $projectmanager->listAction($user["id"])->getContent();
		$files=json_decode($files, true);

		return $this->render('CodebenderUtilitiesBundle:Default:sidebar.html.twig', array('files' => $files));
	}

    /**
     * Downloads a Project
     *
     * @param Integer $id
     * @return Response with appropriate data for downloading project
     */ 
	public function downloadAction($id)
	{
		syslog(LOG_INFO, "project download");
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
		$htmlcode = 200;
		$value = "";

		$projectmanager = $this->get('codebender_project.sketchmanager');

		$name = $projectmanager->getNameAction($id)->getContent();
		$name = json_decode($name, true);
		$name = str_replace(" ","-",$name["response"]);

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		$files = $files["list"];

		if(isset($files[0]))
		{
            //TODO Find a better way to get tmp dir
			$filename = tempnam('/tmp', 'cb_');
			if($filename)
			{
				$zip = new ZipArchive();

				if ($zip->open($filename, ZIPARCHIVE::CREATE)!==true)
				{
					$value = "";
					$htmlcode = 404;
                    $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_PROJECT, 'DOWNLOAD_PROJECT',json_encode(array('success' => false, 'project'=>$id)));

                }
				else
				{
					if($zip->addEmptyDir($name)!==true)
					{
						$value = "";
						$htmlcode = 404;
                        $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_PROJECT, 'DOWNLOAD_PROJECT',json_encode(array('success' => false, 'project'=>$id)));

                    }
					else
					{
						foreach($files as $file)
						{
							$zip->addFromString($name."/".str_replace(" ","-",$file["filename"]), $file["code"]);
						}
						$zip->close();
						$value = file_get_contents($filename);
					}
					unlink($filename);
				}
			}
			else
			{
				$value = "";
				$htmlcode = 404;
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_PROJECT, 'DOWNLOAD_PROJECT',json_encode(array('success' => false, 'project'=>$id)));

            }
		}
		else
		{
			$value = "";
			$htmlcode = 404;
             $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_PROJECT, 'DOWNLOAD_PROJECT',json_encode(array('success' => false, 'project'=>$id)));

        }

		$headers = array('Content-Type'		=> 'application/octet-stream',
			'Content-Disposition' => 'attachment;filename="'.$name.'.zip"');
        if($htmlcode == 200)
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_PROJECT, 'DOWNLOAD_PROJECT',json_encode(array('success' => true, 'project'=>$id)));

		return new Response($value, $htmlcode, $headers);
	}

    /**
     * Downloads an example
     *
     * @param String $name
     * @param String $url
     * @return Response with appropriate data for downloading example
     */
    public function downloadExampleAction($name, $url)
    {
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

        $htmlcode = 200;
        $value = "";

        $utilities = $this->get('codebender_utilities.handler');
        $data = json_decode($utilities->get($this->container->getParameter('library').$url), true);
        $files = $data['files'];

        if(isset($files[0]))
        {
            //TODO Find a better way to get tmp dir
            $filename = tempnam("/tmp", 'cb_');
            if($filename)
            {
                $zip = new ZipArchive();

                if ($zip->open($filename, ZIPARCHIVE::CREATE)!==true)
                {
                    $value = "";
                    $htmlcode = 404;
                    $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_LIBRARY_EXAMPLE, 'DOWNLOAD_LIBRARY_EXAMPLE',json_encode(array('success' => false, 'url'=>$url)));

                }
                else
                {
                    if($zip->addEmptyDir($name)!==true)
                    {
                        $value = "";
                        $htmlcode = 404;
                        $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_LIBRARY_EXAMPLE, 'DOWNLOAD_LIBRARY_EXAMPLE',json_encode(array('success' => false, 'url'=>$url)));

                    }
                    else
                    {

                        foreach($files as $file)
                        {
                            $zip->addFromString($name."/".$file["filename"], $file["code"]);
                        }
                        $zip->close();
                        $value = file_get_contents($filename);

                    }
                    unlink($filename);
                }
            }
            else
            {
                $value = "";
                $htmlcode = 404;
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_LIBRARY_EXAMPLE, 'DOWNLOAD_LIBRARY_EXAMPLE',json_encode(array('success' => false, 'url'=>$url)));

            }
        }
        else
        {
            $value = "";
            $htmlcode = 404;
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_LIBRARY_EXAMPLE, 'DOWNLOAD_LIBRARY_EXAMPLE',json_encode(array('success' => false, 'url'=>$url)));
        }

        $headers = array('Content-Type'		=> 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="'.$name.'.zip"');
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_LIBRARY_EXAMPLE, 'DOWNLOAD_LIBRARY_EXAMPLE',json_encode(array('success' => true, 'url'=>$url)));
        return new Response($value, $htmlcode, $headers);
    }

    /**
     * Saves code in project
     *
     * @param Integer $id
     * @return JSON encoded success or failure
     */
    public function saveCodeAction($id)
	{
		syslog(LOG_INFO, "editor save");
        $files = $this->getRequest()->request->get('data');
        if($files == null)
        {
            return new Response("No data.", 500);
        }
        $files = json_decode($files, true);

        if($files == null)
        {
            return new Response("Wrong data.", 500);
        }

        $userController = $this->get('codebender_user.usercontroller');
		$user = json_decode($userController->getCurrentUserAction()->getContent(), true);
        $userController->updateEditAction();


        $response = null;
		$projectmanager = $this->get('codebender_project.sketchmanager');
		foreach($files as $key => $file)
		{
			$response = $projectmanager->setFileAction($id, $key, htmlspecialchars_decode($file))->getContent();
			$response = json_decode($response, true);
            if($response["success"] ==  false)
            {
                $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::EDIT_PROJECT, 'EDIT_PROJECT', json_encode(array('success' => false, 'project'=>$id)));
                return new Response(json_encode($response));
            }
        }
        $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::EDIT_PROJECT, 'EDIT_PROJECT', json_encode(array('success' => true, 'project'=>$id)));
        return new Response(json_encode($response));
	}

    /**
     * Clones a project
     *
     * @param Integer $id
     * @return Redirect to Project or Index
     */
	public function cloneAction($id)
	{
		syslog(LOG_INFO, "project cloned");

        $userController = $this->get('codebender_user.usercontroller');
		$user = json_decode($userController->getCurrentUserAction()->getContent(), true);
        $userController->updateCloningAction();

		$name = $this->getRequest()->request->get('name');

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->cloneAction($user["id"], $id)->getContent();
		$response = json_decode($response, true);
        if($response['success'])
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::CLONE_PROJECT, 'CLONE_PROJECT', json_encode(array('success' => true, 'project'=>$response["id"], 'parent'=>$id)));
            return $this->redirect($this->generateUrl('CodebenderGenericBundle_project',array('id' => $response["id"])));
        }
		else
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::CLONE_PROJECT, 'CLONE_PROJECT', json_encode(array('success' => false, 'parent'=>$id)));
            $this->get('session')->getFlashBag()->add('error', "Error: ".$response['error']);
            return $this->redirect($this->generateUrl('CodebenderGenericBundle_index'));
        }
	}

    /**
     * Adds a Board
     *
     * @return Redirects user to appropriate location
     */
    public function addBoardAction()
    {
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $boardsmanager = $this->get('codebender_board.defaultcontroller');

        if($_FILES["boards"]["error"]>0)
        {
            $this->get('session')->getFlashBag()->add("error","Error: Upload failed with error code ".$_FILES["boards"]["error"].".");
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>$_FILES["boards"]["error"])));
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }
        if($_FILES["boards"]["type"]!== "text/plain")
        {
            $this->get('session')->getFlashBag()->add("error","Error: File type should be .txt.");
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>'File type should be .txt.')));
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }
        $canAdd = json_decode($boardsmanager->canAddPersonalBoardAction($user['id'])->getContent(), true);

        if(!$canAdd["success"])
        {
            $this->get('session')->getFlashBag()->add("error","Error: Cannot add personal board.");
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>"Cannot add personal board.")));
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }

        $available = $canAdd["available"];
        $parsed = json_decode($boardsmanager->parsePropertiesFileAction(file_get_contents( $_FILES["boards"]["tmp_name"]))->getContent(), true);
        if(!$parsed["success"])
        {
           $this->get('session')->getFlashBag()->add("error","Error: Could not read Board Properties File.");
           if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>"Could not read Board Properties File.")));
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }

        $boards = $parsed['boards'];

        if(count($boards)>$available)
        {
            $this->get('session')->getFlashBag()->add("error","Error: You can add up to ".$available." boards (tried to add ".count($boards).").");
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>"You can add up to ".$available." boards (tried to add ".count($boards).")")));
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }

        foreach ($boards as $b)
        {
            $isBoard = json_decode($boardsmanager->isValidBoardAction($b)->getContent(), true);
            if(!$isBoard["success"])
            {
                $this->get('session')->getFlashBag()->add("error","Error: File does not have the required structure.");
                if($user['success'])
                    $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>"File does not have the required structure.")));
                return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
            }
        }

        foreach ($boards as $b)
        {
            $added = json_decode($boardsmanager->addBoardAction($b, $user['id'])->getContent(), true);
            if(!$added["success"])
            {
                $this->get('session')->getFlashBag()->add("error","Error: Could not add board '".$b["name"]."'. Process stopped.");
                if($user['success'])
                    $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => false, 'error'=>"Could not add board. Process stopped.")));
                return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
            }
        }
        $this->get('session')->getFlashBag()->add("notice",count($boards)." boards were successfully added.");
        if($user['success'])
            $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::UPLOAD_BOARD, 'UPLOAD_BOARD', json_encode(array('success' => true, 'message'=>count($boards)." boards were successfully added.")));
        return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));

    }

    /**
     * Deletes a Board
     *
     * @param Integer $id
     * @return Redirects user to appropriate location
     */
    public function deleteBoardAction($id)
    {
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $boardsmanager = $this->get('codebender_board.defaultcontroller');
        $response = $boardsmanager->deleteBoardAction($id)->getContent();
        $response = json_decode($response, true);
        if($response['success'])
        {
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::DELETE_BOARD, 'DELETE_BOARD', json_encode(array('success' => true)));
            $this->get('session')->getFlashBag()->add("notice",$response['message']);
            return $this->redirect($this->generateUrl("CodebenderGenericBundle_boards"));
        }
        else
        {
            if($user['success'])
                $this->get('codebender_utilities.logcontroller')->logAction($user["id"], Log::DELETE_BOARD, 'DELETE_BOARD', json_encode(array('success' => false, 'error' => $response['message'])));
            $this->get('session')->getFlashBag()->add('error', "Error: ".$response['message']);
            return $this->redirect($this->generateUrl('CodebenderGenericBundle_boards'));
        }

    }

    /**
     * Edits a board
     *
     * @return JSON encoded success or failure
     */
    public function editBoardAction()
    {

        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        $request = $this->getRequest()->request;
        $id = $request->get('id');
        $description = $request->get('desc');
        $name = $request->get('name');
        $boardsmanager = $this->get('codebender_board.defaultcontroller');

        $response = json_decode($boardsmanager->editAction($id, $name, $description)->getContent(), true);
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::EDIT_BOARD, 'EDIT_BOARD', json_encode(array('success' => $response['success'])));
        return new Response(json_encode($response));
    }

    /**
     * Creates a File
     *
     * @param Integer $id
     * @return JSON encoded success of failure
     */
	public function createFileAction($id)
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$data = $this->getRequest()->request->get('data');
		$data = json_decode($data, true);

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->createFileAction($id, $data["filename"], "")->getContent();
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::CREATE_FILE, 'CREATE_FILE', $response);
		$response = json_decode($response, true);
		return new Response(json_encode($response));
	}

    /**
     * Deletes a file
     *
     * @param Integer $id
     * @return JSON encoded success of failure
     */
	public function deleteFileAction($id)
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$data = $this->getRequest()->request->get('data');
		$data = json_decode($data, true);

		$projectmanager = $this->get('codebender_project.sketchmanager');
		$response = $projectmanager->deleteFileAction($id, $data["filename"])->getContent();
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::DELETE_FILE, 'DELETE_FILE', $response);
		$response = json_decode($response, true);
        return new Response(json_encode($response));
	}

    /**
     * Gets users gravatar
     *
     * @return Twig rendered Gravatar
     */
	public function imageAction()
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);

		$utilities = $this->get('codebender_utilities.handler');
		$image = $utilities->get_gravatar($user["email"]);

		return $this->render('CodebenderUtilitiesBundle:Default:image.html.twig', array('user' => $user["username"],'image' => $image));
	}

    /**
     * Compiles a project
     *
     * @return Compiles Response
     * @todo Fail gracefully on wrong request content
     */
	public function compileAction()
	{
		$request_content = $this->getRequest()->getContent();
        if($request_content == null)
        {
            return new Response("No data.", 500);
        }
        $content = json_decode($request_content, true);

        if($content == null)
        {
            return new Response("Wrong data.", 500);
        }
        $userController = $this->get('codebender_user.usercontroller');
        $user = json_decode($userController->getCurrentUserAction()->getContent(), true);
		//TODO: Check this for authenticated/unauthenticated users
        $userController->updateCompileAction();


        //TODO: Test this on a Linux machine
		if($content["format"] == "syntax")
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::VERIFY_PROJECT, 'VERIFY_PROJECT', "");
            syslog(LOG_INFO, "verify");
        }
		else if ($content["format"] == "binary")
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::COMPILE_PROJECT, 'COMPILE_PROJECT', "");
            syslog(LOG_INFO, "flash");
        }

        else if ($content["format"] == "hex")
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']? $user["id"]:null, Log::DOWNLOAD_HEX, 'DOWNLOAD_HEX', "");
        }

		$files = $content["files"];

		$utilities = $this->get('codebender_utilities.handler');

		$headers = $utilities->read_libraries($files);

		$libraries = array();
        $libmanager_url = $this->container->getParameter('library');
		foreach($headers as $header)
		{
			$data = $utilities->get($libmanager_url."/fetch?library=".$header);

			$data = json_decode($data, true);
			if($data["success"])
			{
				$libraries[$header] = $data["files"];
			}
		}

		$content["libraries"] = $libraries;
		$request_content = json_encode($content);

        $data = $utilities->post_raw_data($this->container->getParameter('compiler'), $request_content);

		return new Response($data);

	}

    /**
     * Download Hex
     *
     * @return Appropriate data to download hex
     */
    public function downloadHexAction()
    {
        $data = $this->getRequest()->request->get('hex');
        if( $data == null)
            return new Response("No data.", 500);
        $get = json_decode($data, true);
        if(!$get)
            return new Response("No data.", 500);
        $value = $get['hex'];

        $headers = array('Content-Type'	=> 'application/octet-stream',
            'Content-Disposition' => 'attachment;filename="project.hex"');
        return new Response($value, 200, $headers);
    }

    /**
     * Flashes Board
     *
     * @return Response OK
     */
	public function flashAction()
	{
		/**
         * @todo Check this for authenticated/unauthenticated users
         */
        $userController = $this->get('codebender_user.usercontroller');
        $user = json_decode($userController->getCurrentUserAction()->getContent(), true);
        $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:null, Log::FLASH_PROJECT, 'FLASH_PROJECT', '');
        $userController->updateFlashAction();
		return new Response("OK");
	}

    /**
     * Intercom Communication 
     *
     * @return Response ""
     * @deprecated No longer needed in Bachelor
     */
	public function intercomAction()
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
		if($user["success"])
		{
			//TODO: Make preregistration_date NOT NULLABLE and remove all this shit
			$date = "1352246400";
			if($user["preregistration_date"] != null)
			{
				$new_date = strtotime($user["preregistration_date"]["date"]);
				if($new_date !== false)
					$user["preregistration_date"] = $new_date;
				else
					$user["preregistration_date"] = $date;
			}
			else
				$user["preregistration_date"] = $date;

			if($user["registration_date"] != null)
				$user["registration_date"] = strtotime($user["registration_date"]["date"]);

			if ($user["last_edit"] != null)
				$user["last_edit"] = strtotime($user["last_edit"]["date"]);

			if ($user["last_compile"] != null)
				$user["last_compile"] = strtotime($user["last_compile"]["date"]);

			if ($user["last_flash"] != null)
				$user["last_flash"] = strtotime($user["last_flash"]["date"]);

			if ($user["last_cloning"] != null)
				$user["last_cloning"] = strtotime($user["last_cloning"]["date"]);

			if ($user["actual_last_login"] != null)
				$user["actual_last_login"] = strtotime($user["actual_last_login"]["date"]);

			if ($user["last_walkthrough_date"] != null)
				$user["last_walkthrough_date"] = strtotime($user["last_walkthrough_date"]["date"]);

			return $this->render('CodebenderUtilitiesBundle:Default:intercom.html.twig', array('user' => $user));
		}
		return new Response("");
	}

    /**
     * Olark Information
     *
     * @deprecated No longer needed in Bachelor
     */
	public function olarkAction()
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
		if ($user["success"])
		{
			return $this->render('CodebenderUtilitiesBundle:Default:olark.html.twig', array('user' => $user));
		}
		return new Response("");
	}

    /**
     * Shows Modal window for EULA and Walkthrough
     *
     * @return Rendered Twig template or Blank Response
     */
    public function walkthroughEulaModalAction()
    {
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        if ($user["success"])
        {
            return $this->render('CodebenderUtilitiesBundle:Default:walkthroughEulaModal.html.twig', array('user' => $user));
        }
        return new Response("");
    }

    /**
     * Shows intercome hash
     *
     * @deprecated No longer needed in Bachelor
     */
	public function intercomHashAction($id)
	{
		return new Response(hash_hmac("sha256", $id, $this->container->getParameter('intercom_secret_key')));
	}

    /**
     * Logs a Message to syslog
     *
     * @param String $message
     * @return Response OK
     */
	public function logAction($message)
	{
		syslog(LOG_INFO, "codebender generic log: ".$message);
        $response = new Response("OK");
        $response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}

    /**
     * Logs to Database
     *
     * @return Response OK or Invalid Action ID
     */
    public function logDatabaseAction($actionid, $meta)
    {
        $user = json_decode($this->get('codebender_user.usercontroller')->getCurrentUserAction()->getContent(), true);
        if($actionid == 16)
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:NULL, Log::CLOUD_FLASH_BUTTON, 'CLOUD_FLASH_BUTTON', $meta);
        }
        else if ($actionid == 17)
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:NULL, Log::WEBSERIAL_MONITOR_BUTTON, 'WEBSERIAL_MONITOR_BUTTON', $meta);
        }
        else if ($actionid == 18)
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:NULL, Log::SERIAL_MONITOR_BUTTON, 'SERIAL_MONITOR_BUTTON', $meta);
        }
        else if ($actionid == 25)
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:NULL, Log::UPLOAD_BOOTLOADER_BUTTON, 'UPLOAD_BOOTLOADER_BUTTON', $meta);
        }
        else if ($actionid == 27)
        {
            $this->get('codebender_utilities.logcontroller')->logAction($user['success']?$user["id"]:NULL, Log::PLUGIN_INFO, 'PLUGIN_INFO', $meta);
        }
        else if ($actionid == 28)
        {
	        $this->get('codebender_utilities.logcontroller')->logAction($user['success'] ? $user["id"] : NULL, Log::OS_BROWSER_INFO, 'OS_BROWSER_INFO', $meta);
        }
        else return new Response("Invalid Action ID");

        return new Response("OK");

    }

    /**
     * Accepts EULA
     *
     * @return Redirect or blank Response
     */
	public function acceptEulaAction()
	{
		$user = json_decode($this->get('codebender_user.usercontroller')->setEulaAction()->getContent(), true);
		if ($user["success"])
		{
			return $this->redirect($this->generateUrl('CodebenderGenericBundle_index'));
		}
		return new Response("");
	}

}
