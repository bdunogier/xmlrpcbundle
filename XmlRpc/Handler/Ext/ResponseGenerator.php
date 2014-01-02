<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc\Handler\Ext;

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
    public function fromXmlRpcResponse( Response $xmlRpcResponse )
    {
        return $this->generateResponse(
            $this->encodeResponse( $xmlRpcResponse->return )
        );
    }

    /**
     * Generates an XMLRPC HTTP response for the Exception $e
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromException( Exception $exception )
    {
        return $this->generateResponse(
            xmlrpc_encode_request(
                null,
                array(
                    'faultCode' => $exception->getCode(),
                    'faultString' => $exception->getMessage()
                )
            )
        );
    }

    protected function generateResponse( $content )
    {
        $response = new HttpResponse();
        $response->setStatusCode( 200 );
        $response->headers->set( 'Content-Type', 'text/xml' );
        $response->setContent( $content );
        return $response;
    }

    protected function encodeResponse( $response )
    {
        return xmlrpc_encode_request(
            null,
            $this->fixUpTypes( $response )
        );
    }

    /**
     * Fixes up
     */
    protected function fixUpTypes( $response )
    {
        if ( is_array( $response ) )
        {
            foreach ( $response as &$value )
            {
                $value = $this->fixUpTypes( $value );
            }
        }

        if ( $response instanceof DateTime )
        {
            $response = $response->format( "Ymd\TH:i:s" );
            xmlrpc_set_type( $response, 'datetime' );
        }

        return $response;
    }
}
