<?php
/**
 * File containing the RequestParserTest class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser;
use PHPUnit_Framework_TestCase;

class RequestParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers RequestParser::fromXmlString
     * @covers RequestParser::__construct
     * @returns RequestParser The RequestParser with the loaded string
     */
    public function testLoadXmLString()
    {
        $xmlString = <<< XML
<?xml version="1.0"?>
<methodCall>
  <methodName>bdxmlrpc.getStuff</methodName>
  <params>
    <param>
        <value><i4>42</i4></value>
    </param>
  </params>
</methodCall>
XML;

        $this->getRequestParser()->fromXmlString( $xmlString );
        return $this->getRequestParser();
    }

    /**
     * @covers RequestParser::loadXmlString()
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Invalid XML string:
     */
    public function testFromXmlStringInvalidXml()
    {
        $this->getRequestParser()->fromXmlString( "This is not XML" );
    }

    /**
     * @covers RequestParser::loadXmlString()
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Invalid XML-RPC structure (/methodCall/methodName not found)
     */
    public function testLoadXmlStringInvalidStructure()
    {
        $xmlString = <<< XML
<?xml version="1.0"?>
<someNode>
  <someOtherNode>bdxmlrpc.getStuff</someOtherNode>
</someNode>
XML;

        $this->getRequestParser()->fromXmlString( $xmlString );
    }

    /**
     * @depends testLoadXmLString
     * @param RequestParser $requestParser
     */
    public function testGetMethodName( RequestParser $requestParser)
    {
        self::assertEquals( 'bdxmlrpc.getStuff', $requestParser->getMethodName() );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser
     */
    private function getRequestParser()
    {
        if ( !isset( $this->requestParser ) )
        {
            $this->requestParser = new RequestParser;
        }
        return $this->requestParser;
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser
     */
    private $requestParser;
}