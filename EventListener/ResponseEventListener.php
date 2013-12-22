<?php
/**
 * File containing the ViewEventListener class.
 */

namespace BD\Bundle\XmlRpcBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGeneratorInterface;

class ResponseEventListener implements EventSubscriberInterface
{
    /**
     * @var ResponseGeneratorInterface
     */
    private $responseGenerator;

    public function __construct( ResponseGeneratorInterface $responseGenerator )
    {
        $this->responseGenerator = $responseGenerator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array( array( 'onControllerView', 16 ) ),
            KernelEvents::EXCEPTION => array( array( 'onException', 16 ) )
        );
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onControllerView( GetResponseForControllerResultEvent $event )
    {
        if ( !$event->getControllerResult() instanceof XmlRpcResponse )
            return;

        $event->setResponse(
            $this->responseGenerator->fromXmlRpcResponse( $event->getControllerResult() )
        );
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onException( GetResponseForExceptionEvent $event )
    {
        if ( !$event->getRequest()->attributes->has( 'IsXmlRpcRequest' ) )
            return;

        $event->setResponse(
            $this->responseGenerator->fromException( $event->getException() )
        );
    }
}
