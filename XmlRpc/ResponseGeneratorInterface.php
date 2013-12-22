<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Exception;

/**
 * Generates an HttpFoundation response from an XmlRpc one
 */
interface ResponseGeneratorInterface
{
    /**
     * @param \BD\Bundle\XmlRpcBundle\XmlRpc\Response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromXmlRpcResponse( Response $xmlRpcResponse );

    /**
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fromException( Exception $exception );
}
