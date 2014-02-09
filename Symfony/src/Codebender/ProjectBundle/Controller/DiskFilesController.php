<?php
// src/Codebender/ProjectBundle/Controller/DiskFilesController.php

namespace Codebender\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Codebender\ProjectBundle\Helper\ProjectErrorsHelper;

class DiskFilesController extends FilesController
{
    protected $dir;
    protected $type;
    protected $sc;


    public function createAction()
    {

        $projects = @scandir($this->dir);
        if(!$projects)
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CREATE_PROJ_MSG, array("error" => 'Cannot create project.'));
        $current_user = $this->sc->getToken()->getUser();
        $name = $current_user->getUsername();
        do
        {
            $id = $name."/".uniqid($more_entropy=true);
        } while(in_array($id, $projects));
        if(!is_dir($this->dir.$this->type))
        {
            if(!@mkdir($this->dir.$this->type))
            {
                return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CREATE_PROJ_MSG, array("error" => 'Cannot create project.'));
            }
        }
        if(!is_dir($this->dir.$this->type."/".$name))
        {
            if(!@mkdir($this->dir.$this->type."/".$name))
            {
                return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CREATE_PROJ_MSG, array("error" => 'Cannot create project.'));
            }
        }
        if(!is_dir($this->getDir($id)))
        {
            if(!@mkdir($this->getDir($id)))
            {
                return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CREATE_PROJ_MSG, array("error" => 'Cannot create project.'));
            }
        }
        return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_CREATE_PROJ_MSG, array("id" => $id));
    }

    public function deleteAction($id)
    {
        $dir = $this->getDir($id);
        if($this->deleteDirectory($dir))
            return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_DELETE_PROJ_MSG);
        else
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_DELETE_PROJ_MSG, array("error" => "No projectfiles found with id: ".$id));
    }

    public function listFilesAction($id)
    {
        $list = $this->listFiles($id);
        return json_encode(array("success" => true, "list" => $list));
    }

    public function createFileAction($id, $filename, $code)
    {

        $canCreateFile = json_decode($this->canCreateFile($id, $filename), true);
        if(!$canCreateFile["success"])
            return json_encode($canCreateFile);
        $dir = $this->getDir($id);
        if(file_put_contents($dir."/".$filename,$code) === false)
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CREATE_FILE_MSG, array("id" => $id, "filename" => $filename, "error" => "File could not be created."));
        else
            return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_CREATE_FILE_MSG);
    }

    public function getFileAction($id, $filename)
    {
        $response = array("success" => false);
        $list = $this->listFiles($id);
        foreach($list as $file)
        {
            if($file["filename"] == $filename)
                $response=array("success" => true, "code" => $file["code"]);
        }
        return json_encode($response);
    }

    public function setFileAction($id, $filename, $code)
    {
        $dir = $this->getDir($id);
        $exists = json_decode($this->fileExists($id,$filename), true);
        if($exists['success'])
        {
            if(file_put_contents($dir.$filename,$code) === false)
                return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_SAVE_MSG, array("id" => $id, "filename" => $filename, "error" => "You have no permissions to the directory."));
            else
                return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_SAVE_MSG);
        }
        return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_SAVE_MSG, array("id" => $id, "filename" => $filename));

    }

    public function deleteFileAction($id, $filename)
    {
        $fileExists = json_decode($this->fileExists($id, $filename), true);
        if(!$fileExists["success"])
            return json_encode($fileExists);
        $dir = $this->getDir($id);
        if(!@unlink($dir.$filename))
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_DELETE_FILE_MSG, array("id" => $id, "filename" => $filename, "error" => "You have no permissions to delete this file."));
        else
            return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_DELETE_FILE_MSG);
    }

    public function renameFileAction($id, $filename, $new_filename)
    {
        $fileExists = json_decode($this->fileExists($id, $filename), true);
        if(!$fileExists["success"])
            return json_encode($fileExists);

        $canCreateFile = json_decode($this->canCreateFile($id, $new_filename), true);
        if($canCreateFile["success"])
        {
            $dir = $this->getDir($id);
            if(!@rename($dir.$filename, $dir.$new_filename))
                return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_RENAME_FILE_MSG, array("id" => $id, "filename" => $new_filename, "error" => "You have no permissions to rename this file.", "old_filename" => $filename));
            else
                return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_RENAME_FILE_MSG);
        }
        return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_RENAME_FILE_MSG, array("id" => $id, "filename" => $new_filename, "error" => $canCreateFile['error'], "old_filename" => $filename));
    }


    protected function listFiles($id)
    {
        $dir = $this->getDir($id);
        $list = array();
        $objects = @scandir($dir);
        if($objects)
        {
            foreach ($objects as $object)
            {
                if(!is_dir($dir.$object))
                {
                    $file["filename"] = $object;
                    $file["code"] = file_get_contents($dir.$object);
                    $list[] = $file;
                }
            }
            return $list;
        }
    }


    private function deleteDirectory($dir)
    {
        if (is_dir($dir))
        {
            $objects = @scandir($dir);
            if($objects)
            {
                foreach ($objects as $object)
                {
                    if ($object != "." && $object != "..")
                    {
                        if (filetype($dir."/".$object) == "dir") $this->deleteDirectory($dir."/".$object); else unlink($dir."/".$object);
                    }
                }
                reset($objects);
                rmdir($dir);
                return true;

            }
            else return false;
        }
        else return false;
    }


    public function getDir($id)
    {
        return $this->dir.$this->type."/".$id."/";
    }

    public function __construct($directory, $type, SecurityContext $sc)
    {
        if(strlen($directory)>0 )
            if(!(substr_compare($directory, '/', 1, 1) === 0))
                $directory = $directory.'/';
        $this->dir = $directory;
        $this->type = $type;
        $this->sc = $sc;
    }
}

