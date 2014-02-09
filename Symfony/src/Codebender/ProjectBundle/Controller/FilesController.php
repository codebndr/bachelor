<?php
// src/Codebender/ProjectBundle/Controller/DiskFilesController.php

namespace Codebender\ProjectBundle\Controller;

use Codebender\ProjectBundle\Helper\ProjectErrorsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


abstract class FilesController extends Controller
{

    public abstract function createAction();

    public abstract function deleteAction($id);

    public abstract function listFilesAction($id);

    public abstract function createFileAction($id, $filename, $code);

    public abstract function getFileAction($id, $filename);

    public abstract function setFileAction($id, $filename, $code);

    public abstract function deleteFileAction($id, $filename);

    public abstract function renameFileAction($id, $filename, $new_filename);

    protected abstract function listFiles($id);

    protected function fileExists($id, $filename)
    {
        $list = $this->listFiles($id);
        foreach($list as $file)
        {
            if($file["filename"] == $filename)
                return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_FILE_EXISTS_MSG);
        }

        return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_FILE_EXISTS_MSG, array("filename" => $filename, "error" => "File ".$filename." does not exist."));
    }

    protected function canCreateFile($id, $filename)
    {
        $fileExists = json_decode($this->fileExists($id,$filename),true);
        if($fileExists["success"])
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CAN_CREATE_FILE_MSG, array("id" => $id, "filename" => $filename, "error" => "This file already exists"));
        $validName = json_decode($this->nameIsValid($id, $filename), true);
        if(!$validName['success'])
            return ProjectErrorsHelper::fail(ProjectErrorsHelper::FAIL_CAN_CREATE_FILE_MSG, array("id" => $id, "filename" => $filename, "error" => $validName['error']));

        return ProjectErrorsHelper::success(ProjectErrorsHelper::SUCC_CAN_CREATE_FILE_MSG);


    }

    protected function nameIsValid($id, $name)
    {
	    //TODO: I removed the dots, probably added by baltas here. Make sure my fix doesn't break stuff
        $filename = trim(basename(stripslashes($name)), ".\x00..\x20");
        if($filename == $name && $name != "" && $filename == utf8_encode($filename) && preg_match("/[\\/:\"*?&~<>|]/",$filename) == 0)
            return json_encode(array("success" => true));
        else
		    return json_encode(array("success" => false, "error" => "Invalid File Name. Please enter a new one."));
    }

}

