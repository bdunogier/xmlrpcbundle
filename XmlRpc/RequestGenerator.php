<?php
/**
 * File containing the RequestGenerator class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use BD\Bundle\XmlRpcBundle\XmlRpc\ParametersProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestGenerator implements RequestGeneratorInterface
{
    /** @var RequestParserInterface */
    private $requestParser;

    /**
     * Map of parameters processors, indexed by methodName
     * @var ParametersProcessorInterface[]
     */
    protected $parametersProcessors;

    public function __construct( RequestParserInterface $requestParser, array $parametersProcessors = array() )
    {
        $this->requestParser = $requestParser;
        $this->parametersProcessors = $parametersProcessors;
    }

    public function generateFromRequest( Request $originalRequest )
    {
        $this->requestParser->fromXmlString( $originalRequest->getContent() );

        return Request::create(
            $this->getRoutePath( $this->requestParser ),
            "POST",
            $this->getParameters( $this->requestParser ),
            $originalRequest->cookies->all(),
            array(),
            $originalRequest->server->all(),
            $originalRequest->getContent()
        );
    }

    protected function getRoutePath( RequestParserInterface $requestParser )
    {
        $methodName = $this->requestParser->getMethodName();
        $routePath = "/xmlrpc/$methodName";

        if ( !$parametersProcessor = $this->getParametersProcessor( $methodName ) )
        {
            return $routePath;
        }

        $arguments = $parametersProcessor->getRoutePathArguments( $requestParser->getParameters() );
        if ( !$arguments )
        {
            return $routePath;
        }

        return $routePath . "/" . implode( '/', $arguments );
    }

    /**
     * @param $requestParser RequestParserInterface
     * @return array
     */
    protected function getParameters( RequestParserInterface $requestParser )
    {
        if ( !$parametersProcessor = $this->getParametersProcessor( $requestParser->getMethodName() ) )
        {
            return $requestParser->getParameters();
        }

        return $parametersProcessor->getParameters( $requestParser->getParameters() );
    }

    /**
     * @param string
     * @return ParametersProcessorInterface
     */
    private function getParametersProcessor( $methodName )
    {
        if ( !isset( $this->parametersProcessors[$methodName] ) )
        {
            return false;
        }
        return $this->parametersProcessors[$methodName];
    }
}
