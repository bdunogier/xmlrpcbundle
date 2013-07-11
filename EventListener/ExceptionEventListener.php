<?php
/**
 * File containing the ExceptionEventListener class.
 */

namespace BD\Bundle\XmlRpcBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator;

class ExceptionEventListener implements EventSubscriberInterface
{
    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator
     */
    private $responseGenerator;

    public function __construct( ResponseGenerator $responseGenerator )
    {
        $this->responseGenerator = $responseGenerator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array(
                array( 'onException', 16 ),
            )
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
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
