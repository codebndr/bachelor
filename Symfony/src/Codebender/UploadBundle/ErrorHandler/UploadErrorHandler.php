<?php


namespace Codebender\UploadBundle\ErrorHandler;

use Exception;
use Oneup\UploaderBundle\Uploader\ErrorHandler\ErrorHandlerInterface;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;

class UploadErrorHandler implements ErrorHandlerInterface
{
    public function addException(AbstractResponse $response, Exception $exception)
    {
        $message = $exception->getMessage();
        $response['success'] = false;
        $response['error'] = $message;
    }
}
