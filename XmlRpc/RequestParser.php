<?php
/**
 * File containing the RequestParser class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */ 
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use SimpleXmlElement;

/**
 * Parses an XML RPC request
 */
class RequestParser
{
    /** @var \SimpleXMLElement */
    private $simpleXml;

    /**
     * Loads an XML string for parsing
     * @param $xmlString
     *
     * @throws \UnexpectedValueException If the XML payload could not be parsed
     */
    public function fromXmlString( $xmlString )
    {
        libxml_use_internal_errors( true );
        if ( ( $simpleXml = simplexml_load_string( $xmlString ) ) === false )
        {
            $errors = array();
            foreach( libxml_get_errors() as $error )
                $errors[] = $error->message;
            throw new \UnexpectedValueException( "Invalid XML string:" . implode( "\n", $errors ) );
        }

        $this->simpleXml = $simpleXml;

        if ( !isset( $this->simpleXml->methodName ) )
            throw new \UnexpectedValueException( "Invalid XML-RPC structure (/methodCall/methodName not found)" );
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return (string)$this->simpleXml->methodName;
    }
}
