<?php

namespace Codebender\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Codebender\ProjectBundle\Entity\Project as Project;
use Doctrine\ORM\EntityManager;
use Codebender\ProjectBundle\Controller\MongoFilesController;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\SecurityContext;

class SketchController extends ProjectController
{
    protected $em;
	protected $fc;
    protected $sc;
    protected $sl;


	public function createprojectAction($user_id, $project_name, $code, $isPublic = true)
	{
		$retval;
		$response = parent::createprojectAction($user_id, $project_name, $code, $isPublic)->getContent();
		$response = json_decode($response, true);
		if($response["success"]) {
			$response2 = $this->createFileAction($response["id"], $project_name.".ino", $code)->getContent();
			$response2=json_decode($response2, true);
			if($response2["success"]) {
				$retval = array("success" => true, "id" => $response["id"]);
			} else
				$retval = $response2;
		} else
			$retval = $response;

		return new Response(json_encode($retval));
	}


	public function cloneAction($owner, $id)
	{
        $response = json_decode(parent::cloneAction($owner, $id)->getContent(), true);
		if($response["success"] == true) {
            foreach($response["list"] as $file) {
                if(pathinfo($file["filename"], PATHINFO_EXTENSION)== "ino") {
                    $this->createFileAction($response["id"],$response["name"].".ino",$file["code"]);
                } else {
                $this->createFileAction($response["id"],$file["filename"],$file["code"]);
                }
            }
		    return new Response(json_encode(array("success" => true, "id" => $response["id"])));
		} else {
			return new Response(json_encode($response));
		}

	}

    public function renameAction($id, $new_name)
    {
        $response = json_decode(parent::renameAction($id, $new_name)->getContent(),true);
        if($response["success"]) {
            $output = array("success" => true);

            $project = $this->getProjectById($id);
            $name = $project->getName();

            $filename = $name.".ino";

            $response1 = json_decode($this->renameFileAction($id, $filename, $new_name.".ino.bkp")->getContent(), true);
            if($response1["success"]) {
                $response2 = json_decode($this->renameFileAction($id, $new_name.".ino.bkp", $new_name.".ino")->getContent(), true);
                if($response2["success"]) {
                    $project->setName($new_name);
                    $em = $this->em;
                    $em->persist($project);
                    $em->flush();
                } else {
                    $output = $response2;
                    $output["error"] = "backup file ".$new_name.".ino.bkp"." could not be renamed. ".$output["error"];
                }
            } else {
                $output = $response1;
                $output["error"] = "old file ".$filename." could not be renamed. ".$output["error"];
            }

            return new Response(json_encode($output));
        } else {
            return new Response(json_encode($response));
        }

    }

    protected function canCreateFile($id, $filename)
    {
        $parentCreate = json_decode(parent::canCreateFile($id, $filename), true);
        if($parentCreate["success"]) {
            if(pathinfo($filename, PATHINFO_EXTENSION)== "ino") {
                $inoExists = json_decode($this->inoExists($id), true);
                if($inoExists["success"])
                    return json_encode(array("success" => false, "id" => $id, "filename" => $filename, "error" => "Cannot create second .ino file in the same project"));
            }

            return json_encode(array("success" => true));
        } else
            // @codeCoverageIgnoreStart
            throw new \Exception('This should never happen');
            // @codeCoverageIgnoreEnd
    }

    protected function inoExists($id)
    {
        $list = json_decode($this->listFilesAction($id)->getContent(), true);
        if($list["success"]) {
            foreach($list["list"] as $file) {
                if(pathinfo($file["filename"], PATHINFO_EXTENSION)=="ino")
                    return json_encode(array("success" => true));
            }
        } else {
            return json_encode(array("success" => false, "error" => "Cannot access list of project files."));
        }
        return json_encode(array("success" => false, "error" => ".ino file does not exist."));
    }

	public function __construct(EntityManager $entityManager, DiskFilesController $diskFilesController, SecurityContext $securitycontext)
	{
	    $this->em = $entityManager;
        $this->sc = $securitycontext;
        $this->fc = $diskFilesController;
	}
}
