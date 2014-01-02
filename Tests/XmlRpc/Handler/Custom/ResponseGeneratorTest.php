<?php
namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc\Handler\Custom;

use BD\Bundle\XmlRpcBundle\Tests\XmlRpc\Handler\BaseResponseGeneratorTest;
use BD\Bundle\XmlRpcBundle\XmlRpc\Handler\Custom\ResponseGenerator;

class ResponseGeneratorTest extends BaseResponseGeneratorTest
{
    /**
     * @return ResponseGenerator
     */
    protected function getResponseGenerator()
    {
        if ( !isset( $this->responseGenerator ) )
        {
            $this->responseGenerator = new ResponseGenerator();
        }

        return $this->responseGenerator;
    }

    /**
     * @var ResponseGenerator
     */
    private $responseGenerator;
}
