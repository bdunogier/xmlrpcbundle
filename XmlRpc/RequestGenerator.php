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

class RequestGenerator implements RequestGeneratorInterface
{
    /** @var RequestParserInterface */
    private $requestParser;

    public function __construct( RequestParserInterface $requestParser )
    {
        $this->requestParser = $requestParser;
    }

    public function generateFromRequest( Request $originalRequest )
    {
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
