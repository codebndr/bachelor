<?php

namespace Codebender\UploadBundle\Tests\ErrorHandler;

use Symfony\Component\Security\Acl\Exception\Exception;
use Oneup\UploaderBundle\Uploader\Response\AbstractResponse;
use Codebender\UploadBundle\ErrorHandler\UploadErrorHandler;


class TestResponse extends AbstractResponse{
    public function assemble()
    {

    }
};

class UploadErrorHandlerUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testAddExeption()
    {

        $response = new TestResponse();
        $exception = new \Exception("test exception");

        $errorHandler = new UploadErrorHandler();

        $errorHandler->addException($response, $exception);

        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['error'], "test exception");


    }
}
