<?php
/**
 * File containing the RequestGenerator class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use Symfony\Component\HttpFoundation\Request;
use BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser;

class RequestGenerator
{
    /**
     * @var RequestParser
     */
    private $requestParser;

    public function __construct( RequestParser $requestParser )
    {
        $this->requestParser = $requestParser;
    }

    /**
     * Generates an internal XML RPC request from an HTTP one
     *
     * @param Request $originalRequest
     *
     * @return Request
     */
    public function generateFromRequest( Request $originalRequest )
    {
        // We create a new request, based on the XML payload
        $this->requestParser->fromXmlString( $originalRequest->getContent() );

        return Request::create(
            "/xmlrpc/" . $this->requestParser->getMethodName(),
            "POST",
            $this->requestParser->getParameters(),
            $originalRequest->cookies->all(),
            array(),
            $originalRequest->server->all(),
            $originalRequest->getContent()
        );
    }
}
