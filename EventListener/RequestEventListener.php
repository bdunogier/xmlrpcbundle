<?php
/**
 * File containing the RequestEventListener class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\EventListener;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class RequestEventListener implements EventSubscriberInterface
{
    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser
     */
    private $requestParser;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernel
     */
    private $httpKernel;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct( RequestParser $requestParser, HttpKernel $kernel, RouterInterface $router  )
    {
        $this->requestParser = $requestParser;
        $this->httpKernel = $kernel;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(
                array( 'onKernelRequest', 16 ),
            )
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest( GetResponseEvent $event )
    {
        if ( $event->getRequestType() != HttpKernelInterface::MASTER_REQUEST )
            return false;

        if ( strpos( $event->getRequest()->getPathInfo(), '/xmlrpc2' ) != 0 || $event->getRequest()->getMethod() !== 'POST' )
            return;

        // We create a new request, based on the XML payload
        $this->requestParser->loadXmlString( $event->getRequest()->getContent() );

        $request = $event->getRequest()->duplicate(
            null, null, null, null, null, array( 'path' => '/xmlrpc/' . $this->requestParser->getMethodName() )
        );
        $requestContext = new RequestContext();
        $requestContext->fromRequest( $request );

        $originalContext = $this->router->getContext();
        $this->router->setContext( $requestContext );

        $response = $this->httpKernel->handle( $request );

        $event->setResponse( $response );
        $this->router->setContext( $originalContext );
    }
}
