<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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
        return new HttpResponse( "@todo implement" );
    }
}
