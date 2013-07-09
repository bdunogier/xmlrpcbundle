<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use DomDocument;
use DateTime;

/**
 * Generates an HttpFoundation response from an XmlRpc one
 */
class ResponseGenerator
{
    /**
     * @param \BD\Bundle\XmlRpcBundle\XmlRpc\Response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromXmlRpcResponse( XmlRpcResponse $xmlRpcResponse )
    {

        $response = new HttpResponse( "@todo implement" );
        $response->setStatusCode( 200 );
        $response->headers->set( 'Content-Type', 'text/xml' );
        $response->setContent( $this->generateXml( $xmlRpcResponse->return) );
        return $response;
    }

    /**
     * @param mixed $returnValue
     * @return string
     */
    private function generateXml( $returnValue )
    {
        $domDocument = new DomDocument( '1.0', 'UTF-8' );

        // /methodResponse
        $rootNode = $domDocument->createElement( 'methodResponse' );
        $domDocument->appendChild( $rootNode );

        // /methodResponse/params
        $paramsNode = $domDocument->createElement( 'params' );
        $rootNode->appendChild( $paramsNode );

        // /methodResponse/params/param
        $paramNode = $domDocument->createElement( 'param' );
        $paramsNode->appendChild( $paramNode );

        // /methodResponse/params/param/value
        $valueNode = $domDocument->createElement( 'value' );
        $paramNode->appendChild( $valueNode );

        $valueNode->appendChild(
            $this->handleValue( $returnValue, $domDocument )
        );

        // @todo add formatting (for dev ?)
        return $domDocument->saveXML();
    }

    /**
     * @param mixed        $value
     * @param \DomDocument $domDocument
     *
     * @throws \UnexpectedValueException
     * @return \DOMElement
     */
    private function handleValue( $value, DOMDocument $domDocument )
    {
        switch ( gettype( $value ) )
        {
            case 'integer':
                $node = $domDocument->createElement( 'int', $value );
                break;

            case 'string':
                $node = $domDocument->createElement( 'string', $value );
                break;

            case 'double':
                $node = $domDocument->createElement( 'double', $value );
                break;

            case 'array':
                // array
                if ( key( $value ) === 0 )
                {
                    $node = $domDocument->createElement( 'array' );
                    $dataNode = $domDocument->createElement( 'data' );
                    $node->appendChild( $dataNode );

                    foreach ( $value as $cell )
                    {
                        $valueNode = $domDocument->createElement( 'value' );
                        $valueNode->appendChild( $this->handleValue( $cell, $domDocument ) );
                        $dataNode->appendChild( $valueNode );
                    }
                }
                // hash
                else
                {
                    $node = $domDocument->createElement( 'struct' );

                    foreach ( $value as $name => $cell )
                    {
                        // //member
                        $memberNode = $domDocument->createElement( 'member' );

                        // //member/name
                        $memberNode->appendChild( $domDocument->createElement( 'name', $name ) );

                        // //member/value
                        $valueNode = $domDocument->createElement( 'value' );
                        $memberNode->appendChild( $valueNode );
                        // //member/value/*
                        $valueNode->appendChild( $this->handleValue( $cell, $domDocument ) );

                        $node->appendChild( $memberNode );
                    }
                }
                break;

            case 'object':
                // DateTime is okay
                if ( !$value instanceof DateTime )
                    continue;
                $node = $domDocument->createElement( 'dateTime.iso8601', $value->format( DateTime::ISO8601 ) );
                break;

            default:
                    throw new \UnexpectedValueException( "Unknown return value type" );
        }

        return $node;
    }
}
