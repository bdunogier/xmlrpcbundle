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
     * @covers RequestParser::loadXmlString()
     * @returns The RequestParser with the loaded string
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

    }

    /**
     * @covers RequestParser::loadXmlString()
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Invalid xmlString argument
     */
    public function testLoadXmlStringInvalidXml()
    {
        self::markTestSkipped( "Resolve simplexml_load_string warning first" );
        $xmlString = "This is not XML";
        $this->getRequestParser()->loadXmlString( $xmlString );
    }

    public function testLoadXmlStringInvalidStructure()
    {

    }

    public function getMethodName()
    {
    }

    /**
     * @return RequestParser
     */
    public function getRequestParser()
    {
        return new RequestParser;
    }
}