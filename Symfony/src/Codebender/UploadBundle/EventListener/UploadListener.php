<?php

namespace Codebender\UploadBundle\EventListener;

use Codebender\UtilitiesBundle\Controller\LogController;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\SecurityContext;
use Codebender\ProjectBundle\Controller\ProjectController;
use Oneup\UploaderBundle\Event\ValidationEvent;
use Oneup\UploaderBundle\Uploader\Exception\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\Finder\Finder;
use Codebender\UtilitiesBundle\Entity\Log;

class UploadListener {

    private $logController;
    private $securityContext;
    private $projectController;

    function __construct(LogController $logController, SecurityContext $securityContext, ProjectController $projectController)
    {
        $this->logController = $logController;
        $this->securityContext = $securityContext;
        $this->projectController = $projectController;
    }

    public function onValidate(ValidationEvent $event)
    {
        $request = $event->getRequest();
        $type = $request->request->get('uploadType');
        $current_user = $this->securityContext->getToken()->getUser();
        if($current_user === 'anon.')
        {
            $this->logController->logAction(null, Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'error' => 'Not logged in')));
            throw new ValidationException('Error: Please login to upload files.');
        }
        if($type == 'project')
        {
            $supportedMimeTypes = array('text/plain', 'text/x-c', 'text/x-h', 'application/zip');
            $supportedExtensions = array('ino', 'zip');
            $file = $event->getFile();
            if(!in_array($file->getMimeType(), $supportedMimeTypes) or !in_array($file->getExtension(), $supportedExtensions))
            {
                $this->logController->logAction($current_user->getId(), Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'error' => 'Validation Error on file', 'mimeType' => $file->getMimeType(), 'extension' => $file->getExtension())));
                throw new ValidationException('Error: Invalid file type. Please upload .ino sketches, or zip for projects with multiple files.');
            }
        }
        else if($type == 'file')
        {
            $supportedMimeTypes = array('text/plain', 'text/x-c', 'text/x-h');
            $supportedExtensions = array('h', 'c', 'cpp', 's');
            $file = $event->getFile();
            if(!in_array($file->getMimeType(), $supportedMimeTypes) or !in_array($file->getExtension(), $supportedExtensions))
            {
                $this->logController->logAction($current_user->getId(), Log::UPLOAD_FILE, 'UPLOAD_FILE', json_encode(array('success' => false, 'error' => 'Validation Error on file', 'mimeType' => $file->getMimeType(), 'extension' => $file->getExtension())));
                throw new ValidationException('Error: Invalid file type. Allowed file types are .h, .c, .cpp and .s');
            }
        }
        else
            throw new ValidationException('Error: Upload type not defined.');
    }

    public function onUpload(PostPersistEvent $event)
    {
        $tempfile = $event->getFile();
        $response = $event->getResponse();
        $request = $event->getRequest();
        $type = $request->request->get('uploadType');
        $response['type'] = $type;
        $current_user = $this->securityContext->getToken()->getUser();
        $json_user = json_decode($current_user);
        if($type == 'project')
        {
            $is_public = true;
            if($request->request->get('isPublic') !== null)
            {
                $is_public = $request->request->get('isPublic') === 'true' ? true : false;
            }

            if($tempfile->getExtension() == "ino")
            {

                $originalFilename = $request->files->get('file')->getClientOriginalName();

                $project_name = substr($originalFilename, 0, strlen($originalFilename) -4);
                $created = json_decode($this->projectController->createprojectAction($current_user,$project_name,file_get_contents($tempfile->getPathName()),$is_public) -> getContent(), true);
                if($created['success'])
                {
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => true, 'id' => $created['id'], 'isPublic' => $is_public, 'type' => 'ino')));
                    $response['success'] = true;
                    $response['id'] = $created['id'];
                    $response['is_public'] = $is_public;
                    $response['project_name'] = $project_name;
                    $response['files'] = array($originalFilename);
                }
                else
                {
                    unlink($tempfile->getPathName());
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'ino', 'error' => $created['error'])));
                    throw new UploadException($created['error']);
                }
            }
            else if ($tempfile->getExtension() == "zip")
            {
                $zip = new \ZipArchive();
                $opened = $zip->open($tempfile->getPathName());
                if($opened !== TRUE)
                {
                    unlink($tempfile->getPathName());
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => "Could not open zip file.")));
                    throw new UploadException("Could not open zip file.");
                }
                $filesPath = $tempfile->getPath().'/'.$tempfile->getBaseName(".zip");
                $extracted = $zip->extractTo($filesPath);
                if($extracted !== TRUE)
                {
                    unlink($tempfile->getPathName());
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => "Could not extract zip file.")));
                    throw new UploadException("Could not extract zip file.");
                }

                $filenames = array();

                $inofinder = new Finder();
                $inofinder->in($filesPath);
                $inofinder->name('*.ino');

                if(iterator_count($inofinder) == 0 )
                {
                    unlink($tempfile->getPathName());
                    $this->unlinkDir($filesPath);
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => "Tried to Upload a project with more no ino.")));
                    throw new UploadException("Cannot create project not containing an .ino file.");
                }
                else if (iterator_count($inofinder) > 1)
                {
                    unlink($tempfile->getPathName());
                    $this->unlinkDir($filesPath);
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => "Tried to Upload a project with more than one ino files.")));
                    throw new UploadException("Cannot create project with more than one .ino files.");
                }

                foreach($inofinder as $file)
                {
                    $ino = $file;
                }
                $filenames[] = $ino->getFileName();


                $other = array();
                $otherfinder = new Finder();
                $otherfinder->in($filesPath);
                $otherfinder->name('*.h')->name('*.c')->name('*.cpp')->name('*.s');

                foreach($otherfinder as $file)
                {
                if($file->getPath() != $ino->getPath())
                {
                    unlink($tempfile->getPathName());
                    $this->unlinkDir($filesPath);
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => "Subdirectories are not supported. Project files should be on the same directory.")));
                    throw new UploadException("Subdirectories are not supported. Project files should be on the same directory.");
                }
                $other[] = $file;
                $filenames[] = $file->getFileName();
                }

                $created = json_decode($this->projectController->createprojectAction($current_user, $ino->getBaseName('.ino'), $ino->getContents(), $is_public) -> getContent(), true);
                if(!$created['success'])
                {
                    $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => $created['error'])));
                    unlink($tempfile->getPathName());
                    $this->unlinkDir($filesPath);
                    throw new UploadException($created['error']);
                }
                foreach($other as $file)
                {
                    $fileCreated = json_decode($this->projectController->createFileAction($created['id'], $file->getFileName(), $file->getContents()) ->getContent() , true);
                    if(!$fileCreated['success'])
                    {
                        $this->projectController->deleteAction($created['id']);
                        $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => false, 'type' => 'zip', 'error' => $fileCreated['error'])));
                        unlink($tempfile->getPathName());
                        $this->unlinkDir($filesPath);
                        throw new UploadException($fileCreated['error']);
                    }
                }
                $response['success'] = true;
                $response['id'] = $created['id'];
                $response['is_public'] = $is_public;
                $response['project_name'] = $ino->getBaseName('.ino');
                $response['files'] = $filenames;
                $this->unlinkDir($filesPath);
                $this->logController->logAction($json_user["id"], Log::UPLOAD_PROJECT, 'UPLOAD_PROJECT', json_encode(array('success' => true, 'id' => $created['id'], 'isPublic' => $is_public, 'type' => 'zip')));
            }
            else
            {
                unlink($tempfile->getPathName());
                throw new \Exception("Error: Could not create project.");
            }


        }
        else if ($type == 'file')
        {
            $id = $request->request->get('projectId');

            $fileCreated = json_decode($this->projectController->createFileAction($id, $request->files->get('file')->getClientOriginalName(), file_get_contents($tempfile->getPathName())) ->getContent() , true);
            if(!$fileCreated['success'])
            {
                $this->logController->logAction($json_user["id"], Log::UPLOAD_FILE, 'UPLOAD_FILE', json_encode(array('success' => false, 'projectId' => $id, 'error' => $fileCreated['error'])));
                unlink($tempfile->getPathName());
                throw new UploadException($fileCreated['error']);
            }
            else
            {
                $this->logController->logAction($json_user["id"], Log::UPLOAD_FILE, 'UPLOAD_FILE', json_encode(array('success' => true, 'projectId' => $id)));
                $response['success'] = true;
                $response['filename'] = $request->files->get('file')->getClientOriginalName();
                $response['code'] = file_get_contents($tempfile->getPathName());
            }
        }

        unlink($tempfile->getPathName());
    }

    private function unlinkDir($dir) {
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            if (!$this->unlinkDir($dir . DIRECTORY_SEPARATOR . $file)) {
                chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
                if (!$this->unlinkDir($dir . DIRECTORY_SEPARATOR . $file)) return false;
            };
        }
        return rmdir($dir);
    }

}


