<?php

namespace Codebender\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Codebender\ProjectBundle\Controller\SketchController;

class EditorController extends Controller
{		
	/**
	 * Edit action for the Editor
	 * 
	 * @param Integer $id
	 * @return Rendered template of the editor for project_id, project_name, files, etc.
	 */
	public function editAction($id)
	{
		/** 
		 * @var SketchController $projectmanager 
		 */
		$projectmanager = $this->get('codebender_project.sketchmanager');

		$permissions = json_decode($projectmanager->checkWriteProjectPermissionsAction($id)->getContent(), true);
		if(!$permissions["success"])
		{
			return $this->forward('CodebenderGenericBundle:Default:project', array("id"=> $id));
		}

		$name = $projectmanager->getNameAction($id)->getContent();
		$name = json_decode($name, true);
		$name = $name["response"];

		$is_public = json_decode($projectmanager->getPrivacyAction($id)->getContent(), true);
		$is_public = $is_public["response"];

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		$files = $files["list"];

		foreach($files as $key=>$file)
		{
			$files[$key]["code"] = htmlspecialchars($file["code"]);
		}

		$boardcontroller = $this->get('codebender_board.defaultcontroller');
		$boards = $boardcontroller->listAction()->getContent();

		return $this->render('CodebenderGenericBundle:Editor:editor.html.twig', array('project_id' => $id, 'project_name' => $name, 'files' => $files, 'boards' => $boards, "is_public" => $is_public));
	}
}
