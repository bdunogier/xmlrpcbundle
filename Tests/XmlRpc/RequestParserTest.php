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
use DateTime;

class RequestParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers RequestParser::fromXmlString
     * @covers RequestParser::__construct
     * @returns RequestParser The RequestParser with the loaded string
     */
    public function testLoadXmlString( $xmlString = null )
    {
        if ( !isset( $xmlString ) )
        {
            $xmlString = <<< XML
<?xml version="1.0"?>
<methodCall>
  <methodName>bdxmlrpc.getStuff</methodName>
  <params>
    <param><value><i4>42</i4></value></param>
    <param><value><int>84</int></value></param>

    <param><value><string>Forty-two</string></value></param>
    <param><value>Forty-two again</value></param>

    <param><value><double>-42.21</double></value></param>

    <param><value><dateTime.iso8601>20081219T20:01:00</dateTime.iso8601></value></param>

    <param><value><base64>PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxtZXRob2RDYWxsPgogIDxtZXRob2ROYW1lPmJkeG1scnBjLnRlc3Q8L21ldGhvZE5hbWU+CiAgPHBhcmFtcz4KICAgIDxwYXJhbT4KICAgICAgICA8dmFsdWU+PGk0PjQyPC9pND48L3ZhbHVlPgogICAgPC9wYXJhbT4KICA8L3BhcmFtcz4KPC9tZXRob2RDYWxsPgo=</base64></value></param>

    <param>
        <value>
            <struct>
                <member>
                    <name>int_element</name>
                    <value><int>42</int></value>
                </member>
                <member>
                    <name>string_element</name>
                    <value>Forty-two</value>
                </member>
            </struct>
        </value>
    </param>

    <param>
        <value>
            <array>
                <data>
                    <value><int>42</int></value>
                    <value>Forty-two</value>
                    <value><dateTime.iso8601>20081219T20:01:00</dateTime.iso8601></value>
                </data>
            </array>
        </value>
    </param>
  </params>
</methodCall>
XML;
        }

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
     * @depends testLoadXmlString
     * @param RequestParser $requestParser
     */
    public function testGetMethodName( RequestParser $requestParser)
    {
        self::assertEquals( 'bdxmlrpc.getStuff', $requestParser->getMethodName() );
    }

    /**
     * @depends  testLoadXmlString
     */
    public function testGetArgumentsNoArguments()
    {
        $xmlString = <<< XML
<?xml version="1.0"?>
<methodCall>
  <methodName>bdxmlrpc.testGetParametersNone</methodName>
</methodCall>
XML;
        $parser = $this->testLoadXmLString( $xmlString );
        self::assertEquals(
            array(),
            $parser->getParameters()
        );
    }

    /**
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     * @depends  testLoadXmlString
     */
    public function testGetParametersNoneAgain()
    {
        $xmlString = <<< XML
<?xml version="1.0"?>
<methodCall>
  <methodName>bdxmlrpc.testGetParametersNoArguments</methodName>
  <params />
</methodCall>
XML;
        $parser = $this->testLoadXmLString( $xmlString );
        self::assertEquals(
            array(),
            $parser->getParameters()
        );
    }

    /**
     * @param RequestParser $requestParser A RequestParser instance with a loaded, valid XML
     * @depends testLoadXmlString
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     * @return array Parameters from $requestParser
     */
    public function testGetParameters( RequestParser $requestParser )
    {
        self::assertInternalType( 'array', $requestParser->getParameters() );
        return $requestParser->getParameters();
    }

    /**
     * Tests <int> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetIntegerParameter( $parameters )
    {
        self::assertEquals( 42, $parameters[0] );
    }

    /**
     * Tests <i4> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetI4Parameter( $parameters )
    {
        self::assertEquals( 84, $parameters[1] );
    }

    /**
     * Tests <string> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetStringParameter( $parameters )
    {
        self::assertEquals( "Forty-two", $parameters[2] );
    }

    /**
     * Tests implicit string parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetImplicitStringParameter( $parameters )
    {
        self::assertEquals( "Forty-two again", $parameters[3] );
    }

    /**
     * Tests double parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetDoubleParameter( $parameters )
    {
        self::assertEquals( -42.21, $parameters[4] );
    }

    /**
     * Tests <dateTime.iso8601> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetDateTimeParameter( $parameters )
    {
        self::assertEquals(
            new DateTime( '2008-12-19 20:01:00' ),
            $parameters[5]
        );
    }

    /**
     * Tests <base64> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetBase64Parameter( $parameters )
    {
        self::assertEquals(
            file_get_contents( realpath( __DIR__ . '/../../Resources/doc/xmlrpc/request.xml' ) ),
            $parameters[6]
        );
    }

    /**
     * Tests <array> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetStructParameter( $parameters )
    {
        self::assertEquals(
            array( 'int_element' => 42, 'string_element' => 'Forty-two' ),
            $parameters[7]
        );
    }

    /**
     * Tests <array> parameters
     * @depends testGetParameters
     * @param array Result from RequestParser::getParameters()
     * @covers \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser::getParameters
     */
    public function testGetArrayParameter( $parameters )
    {
        self::assertEquals(
            array(
                42,
                'Forty-two',
                new DateTime( '2008-12-19 20:01:00' )
            ),
            $parameters[8]
        );
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