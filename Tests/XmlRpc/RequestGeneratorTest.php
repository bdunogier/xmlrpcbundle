<?php
namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestGenerator;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class RequestGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers RequestGenerator::generateFromRequest()
     */
    public function testGenerateFromRequest()
    {
        $methodName = 'methodName';
        $parameters = array( 'ping', 'pong' );

        $request = Request::create( '/xmlrpc2', 'POST' );

        $this->getRequestParserMock()
            ->expects( $this->any() )
            ->method( 'getMethodName' )
            ->will( $this->returnValue( $methodName ) );

        $this->getRequestParserMock()
            ->expects( $this->once() )
            ->method( 'getParameters' )
            ->will( $this->returnValue( $parameters ) );

        $requestGenerator = new RequestGenerator( $this->getRequestParserMock() );
        $xmlRpcRequest = $requestGenerator->generateFromRequest( $request );

        self::assertEquals(
            '/xmlrpc/' . $methodName,
            $xmlRpcRequest->getPathInfo()
        );

        self::assertEquals(
            'POST',
            $xmlRpcRequest->getMethod()
        );

        self::assertEquals(
            $parameters,
            $xmlRpcRequest->request->all()
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestParserMock()
    {
        if ( !isset( $this->requestParserMock ) )
        {
            $this->requestParserMock = $this->getMock( 'BD\Bundle\XmlRpcBundle\XmlRpc\RequestParserInterface' );
        }
        return $this->requestParserMock;
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestParserMock;
}
