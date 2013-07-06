<?php
/**
 * File containing the RequestParserTest class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestGenerator;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit_Framework_TestCase;

class RequestParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers RequestGenerator::generateFromRequest()
     */
    public function testGenerateFromRequest()
    {
        $methodName = 'methodName';

        $request = Request::create( '/xmlrpc2', 'POST' );

        $this->getRequestParserMock()
            ->expects( $this->once() )
            ->method( 'getMethodName' )
            ->will( $this->returnValue( $methodName ) );

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
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestParserMock()
    {
        if ( !isset( $this->requestParserMock ) )
        {
            $this->requestParserMock = $this->getMock( 'BD\\Bundle\\XmlRpcBundle\\XmlRpc\\RequestParser' );
        }
        return $this->requestParserMock;
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestParserMock;
}
