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
use DateTime;

/**
 * Parses an XML RPC request
 */
class RequestParser
{
    const XML_RPC_ISO8601 = "Y#m#d\TH#i#s";

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
     * @return array()
     */
    public function getParameters()
    {
        if ( !isset( $this->simpleXml->params ) || $this->simpleXml->params->param->count() == 0 )
            return array();

        $parameters = array();
        foreach ( $this->simpleXml->params->children() as $param )
        {
            if ( !isset( $param->value ) )
            {
                throw new \UnexpectedValueException( "Invalid <param> tag (no value)" );
            }
            $parameters[] = $this->processParameter( (array)$param->value );
        }
        return $parameters;
    }

    protected function processParameter( array $parameter )
    {
        $type = $this->getParameterType( key( $parameter ) );
        $value = current( $parameter );

        switch ( $type )
        {
            case 'int':
                $value = (int)$value;
                break;

            case 'string':
                $value = (string)$value;
                break;

            case 'double':
                $value = (float)$value;
                break;

            case 'boolean':
                $value = (bool)$value;
                break;

            case 'nil':
                $value = null;
                break;

            case 'dateTime':
                $value = $this->handleDateParameter( $value );
                break;

            case 'base64':
                $value = base64_decode( (string)$value );
                break;

            case 'struct':
                $value = $this->handleStructParameter( $value );
                break;

            case 'array':
                $value = $this->handleArrayParameter( $value );
                break;
        }

        return $value;
    }

    /**
     * Parses a dateTime string. ISO-8601 is quite permissive as it makes the separators optional...
     * What a *great* idea.
     *
     * @param string
     *
     * @return DateTime
     */
    protected function handleDateParameter( $value )
    {
        $date = DateTime::createFromFormat(
            self::XML_RPC_ISO8601, (string)$value
        );

        //no separators, maybe, try again
        if ( $date instanceof DateTime )
            return $date;

        $parts = explode( 'T', $value );

        if ( count( $parts ) != 2 )
            throw new \UnexpectedValueException( "Invalid date $value" );
        if ( strlen( $parts[0] ) !== 8 || strlen( $parts[1] ) != 8)
            throw new \UnexpectedValueException( "Invalid date $value" );

        $dateParts = array();
        sscanf( $parts[0], '%04d%02d%02d', $dateParts['year'], $dateParts['month'], $dateParts['day'] );
        sscanf( $parts[1], '%02d:%02d:%02d', $dateParts['hours'], $dateParts['minutes'], $dateParts['seconds'] );
        $iso8601date = sprintf(
            '%4d-%02d-%02dT%02d:%02d:%02d',
            $dateParts['year'], $dateParts['month'], $dateParts['day'],
            $dateParts['hours'], $dateParts['minutes'], $dateParts['seconds']
        );

        return DateTime::createFromFormat( self::XML_RPC_ISO8601, $iso8601date );
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function handleStructParameter( $value )
    {
        $values = array();
        foreach ( $value->member as $value )
        {
            $values[(string)$value->name] = $this->processParameter( (array)$value->value );
        }

        return $values;
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function handleArrayParameter( $value )
    {
        $values = array();
        foreach ( $value->data->value as $value )
        {
            $values[] = $this->processParameter( (array)$value );
        }

        return $values;
    }

    protected function getParameterType( $typeIdentifier )
    {
        $map = array(
            'i4' => 'int',
            'int' => 'int',
            'string' => 'string',
            '0' => 'string',
            'double' => 'double',
            'dateTime.iso8601' => 'dateTime',
            'base64' => 'base64',
            'struct' => 'struct',
            'array' => 'array',
            'boolean' => 'boolean',
            'nil' => 'nil'
        );

        if ( !isset( $map[$typeIdentifier] ) )
            throw new \UnexpectedValueException( "Unknown parameter type '$typeIdentifier'" );

        return $map[$typeIdentifier];
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return (string)$this->simpleXml->methodName;
    }
}
