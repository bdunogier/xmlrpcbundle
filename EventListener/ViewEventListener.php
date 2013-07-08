<?php
/**
 * File containing the ViewEventListener class.
 */

namespace BD\Bundle\XmlRpcBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;
use BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator;

class ViewEventListener implements EventSubscriberInterface
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
            KernelEvents::VIEW => array(
                array( 'onControllerView', 16 ),
            )
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onControllerView( GetResponseForControllerResultEvent $event )
    {
        if ( !$event->getControllerResult() instanceof XmlRpcResponse )
            return;

        $event->setResponse(
            $this->responseGenerator->fromXmlRpcResponse( $event->getControllerResult() )
        );
    }
}
