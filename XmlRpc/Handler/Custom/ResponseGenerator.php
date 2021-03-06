<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc\Handler\Custom;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response;
use BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGeneratorInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Exception;
use DomDocument;
use DateTime;

/**
 * Generates an HttpFoundation response from an XmlRpc one
 */
class ResponseGenerator implements ResponseGeneratorInterface
{
    /**
     * Generates an XML-RPC HTTP response for $xmlRpcResponse
     * @param \BD\Bundle\XmlRpcBundle\XmlRpc\Response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromXmlRpcResponse( Response $xmlRpcResponse )
    {
        $response = new HttpResponse();
        $response->setStatusCode( 200 );
        $response->headers->set( 'Content-Type', 'text/xml' );
        $response->setContent( $this->generateXml( $xmlRpcResponse->return ) );
        return $response;
    }

    /**
     * Generates an XMLRPC HTTP response for the Exception $e
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromException( Exception $exception )
    {
        $response = new HttpResponse();
        $response->setStatusCode( 200 );
        $response->headers->set( 'Content-Type', 'text/xml' );
        $response->setContent( $this->generateXmlFromException( $exception ) );
        return $response;
    }

    /**
     * @param Exception $exception
     * @return string
     */
    private function generateXmlFromException( Exception $exception )
    {
        $domDocument = new DomDocument( '1.0', 'UTF-8' );

        // /methodFault
        $rootNode = $domDocument->createElement( 'methodResponse' );
        $domDocument->appendChild( $rootNode );

        // /methodFault/fault
        $faultNode = $domDocument->createElement( 'fault' );
        $rootNode->appendChild( $faultNode );

        // /methodFault/fault/value
        $valueNode = $domDocument->createElement( 'value' );
        $faultNode->appendChild( $valueNode );

        $array = array(
            'faultCode' => $exception->getCode(),
            'faultString' => $exception->getMessage()
        );
        $valueNode->appendChild(
            $this->handleValue( $array, $domDocument )
        );

        return $domDocument->saveXML();
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

            case 'boolean':
                $node = $domDocument->createElement( 'boolean', $value );
                break;

            case 'string':
                $node = $domDocument->createElement( 'string', $value );
                break;

            case 'double':
                $node = $domDocument->createElement( 'double', $value );
                break;

            case 'array':
                // array
                if ( empty( $value ) || key( $value ) === 0 )
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
                {
                    // DateTime is okay
                    if ( !$value instanceof DateTime )
                        continue;
                    $node = $domDocument->createElement( 'dateTime.iso8601', $value->format( DateTime::ISO8601 ) );
                }
                break;

            default:
                {
                    throw new \UnexpectedValueException( "Unknown return value type" );
                }
        }

        return $node;
    }
}
