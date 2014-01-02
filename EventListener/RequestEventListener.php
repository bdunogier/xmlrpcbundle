<?php
/**
 * File containing the RequestEventListener class.
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\EventListener;

use BD\Bundle\XmlRpcBundle\XmlRpc\RequestGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use UnexpectedValueException;

class RequestEventListener implements EventSubscriberInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestGeneratorInterface
     */
    private $requestGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct( HttpKernelInterface $kernel, RouterInterface $router, RequestGeneratorInterface $requestGenerator, LoggerInterface $logger = null )
    {
        $this->httpKernel = $kernel;
        $this->router = $router;
        $this->requestGenerator = $requestGenerator;
        $this->logger = $logger;
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
            return;

        // @todo make endpoint(s) customizable
        if ( $event->getRequest()->getMethod() !== 'POST' )
            return;

        if ( $event->getRequest()->getPathInfo() != '/xmlrpc' && $event->getRequest()->getPathInfo() != '/xmlrpc.php' )
            return;

        try
        {
            $request = $this->requestGenerator->generateFromRequest( $event->getRequest() );
            if ( isset( $this->logger ) )
            {
                $this->logger->debug( (string)$request );
            }
        }
        catch ( UnexpectedValueException $e )
        {
            $event->setResponse( new Response( "Invalid request XML\n" . $e->getMessage(), 400 ) );
            return;
        }

        // @todo refactor to dynamically set follow-up events instead of testing (cors bundle like)
        $request->attributes->set( 'IsXmlRpcRequest', true );

        $requestContext = new RequestContext();
        $requestContext->fromRequest( $request );

        $originalContext = $this->router->getContext();
        $this->router->setContext( $requestContext );

        $response = $this->httpKernel->handle( $request );

        $event->setResponse( $response );
        $this->router->setContext( $originalContext );

        if ( $response instanceof Response )
            $event->setResponse( $response );
    }
}
