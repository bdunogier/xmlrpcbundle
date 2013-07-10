<?php
/**
 * File containing the ResponseGeneratorTest class.
 */

namespace BD\Bundle\XmlRpcBundle\Tests\XmlRpc;

use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator;
use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use DateTime;

class ResponseGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider responseProvider
     * @param mixed $responseValue
     * @param string $expectations
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testFromXmlRpcResponse( $responseValue, $expectation )
    {
        $xmlRpcResponse = new XmlRpcResponse( $responseValue );

        $response = $this->getResponseGenerator()->fromXmlRpcResponse( $xmlRpcResponse );

        self::assertInstanceOf( '\\Symfony\\Component\\HttpFoundation\\Response', $response );
        self::assertEquals( 200, $response->getStatusCode() );
        self::assertEquals( 'text/xml', $response->headers->get( 'Content-Type' ) );

        $expectedXml = <<< XML
<?xml version="1.0"?>
<methodResponse>
    <params>
        <param>
            <value>
                {$expectation}
            </value>
        </param>
    </params>
</methodResponse>
XML;
        $expectedDom = new \DOMDocument;
        $expectedDom->loadXML( $expectedXml );

        $resultDom = new \DOMDocument();
        $resultDom->loadXML( $response->getContent() );

        self::assertEqualXMLStructure(
            $expectedDom->firstChild,
            $resultDom->firstChild
        );

        return $response;
    }

    public function testFromException()
    {
        $response = $this->getResponseGenerator()->fromException(
            new \Exception( "Forty-two.", 42 )
        );

        self::assertInstanceOf( '\\Symfony\\Component\\HttpFoundation\\Response', $response );
        self::assertEquals( 200, $response->getStatusCode() );
        self::assertEquals( 'text/xml', $response->headers->get( 'Content-Type' ) );

        $expectedXml = <<< XML
<?xml version="1.0"?>
<methodResponse>
    <fault>
        <value>
            <struct>
                <member>
                    <name>faultCode</name>
                    <value><int>42</int></value>
                </member>
                <member>
                    <name>faultString</name>
                    <value><string>Forty-two.</string></value>
                </member>
            </struct>
        </value>
    </fault>
</methodResponse>
XML;
        $expectedDom = new \DOMDocument;
        $expectedDom->loadXML( $expectedXml );

        $resultDom = new \DOMDocument();
        $resultDom->loadXML( $response->getContent() );

        self::assertEqualXMLStructure(
            $expectedDom->firstChild,
            $resultDom->firstChild
        );

        return $response;
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator
     */
    public function getResponseGenerator()
    {
        if ( !isset( $this->responseGenerator ) )
        {
            $this->responseGenerator = new ResponseGenerator();
        }

        return $this->responseGenerator;
    }

    public function responseProvider()
    {
        return array(
            array( 42, '<int>42</int>' ),
            array( 3.1459, '<double>3.1459</double>' ),
            array( 'Forty-two', '<string>Forty-two</string>' ),
            array( new DateTime( '1948-04-28' ), '<dateTime.iso8601>1948-04-28T00:00:00</dateTime.iso8601>' ),
            array( array( 1, 'two' ), '<array><data><value><int>1</int></value><value><string>two</string></value></data></array>' ),
            array( array(), '<array><data /></array>' ),
            array(
                array( 'key1' => 1, 'key2' => 'two' ),
                '<struct>
                    <member><name>key1</name><value><int>1</int></value></member>
                    <member><name>key2</name><value><string>two</string></value></member>
                </struct>'
            )
        );
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator
     */
    private $responseGenerator;
}
