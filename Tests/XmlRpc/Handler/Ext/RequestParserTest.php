<?php
namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc\Handler\Ext;

use BD\Bundle\XmlRpcBundle\Tests\XmlRpc\Handler\BaseRequestParserTest;
use BD\Bundle\XmlRpcBundle\XmlRpc\Handler\Ext\RequestParser;

class RequestParserTest extends BaseRequestParserTest
{
    /**
     * @return RequestParser
     */
    protected function getRequestParser()
    {
        if ( !isset( $this->requestParser ) )
        {
            $this->requestParser = new RequestParser;
        }
        return $this->requestParser;
    }

    /**
     * @var RequestParser
     */
    private $requestParser;
}
