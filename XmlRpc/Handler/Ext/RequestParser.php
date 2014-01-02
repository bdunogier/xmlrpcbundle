<?php
/**
 * File containing the RequestParser class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */ 
namespace BD\Bundle\XmlRpcBundle\XmlRpc\Handler\Ext;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestParserInterface;
use DateTime;
use DateTimeZone;
use stdClass;
use UnexpectedValueException;

/**
 * Parses an XML RPC request
 */
class RequestParser implements RequestParserInterface
{
    protected $methodName;

    protected $parameters;

    public function fromXmlString( $xmlString )
    {
        $this->parameters = xmlrpc_decode_request( $xmlString, $this->methodName );

        $this->fixupParameter( $this->parameters );

        if ( $this->methodName === null )
        {
            throw new UnexpectedValueException( "Invalid XML-RPC structure (/methodCall/methodName not found)" );
        }

        if ( $this->parameters === null )
        {
            if ( ( $simpleXml = @simplexml_load_string( $xmlString ) ) === false )
            {
                $errors = array();
                foreach( libxml_get_errors() as $error )
                    $errors[] = $error->message;
                throw new UnexpectedValueException( "Invalid XML-RPC message:" . implode( "\n", $errors ) );
            }
        }
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Fixes up parameters that aren't properly handled by ext/xmlrpc
     */
    protected function fixupParameter( &$parameter )
    {
        if ( is_array( $parameter ) )
        {
            foreach ( $parameter as &$subParameter )
            {
                $this->fixupParameter( $subParameter );
            }
        }

        if ( $parameter instanceof stdClass )
        {
            if ( isset( $parameter->xmlrpc_type ) && $parameter->xmlrpc_type == 'datetime' )
            {
                $parameter = new DateTime( date( "Ymd\TH:i:s", $parameter->timestamp ) );
            }
            else if ( isset( $parameter->xmlrpc_type ) && $parameter->xmlrpc_type == 'base64' )
            {
                $parameter = (string)$parameter->scalar;
            }
        }
    }
}
