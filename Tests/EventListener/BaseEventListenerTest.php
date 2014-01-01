<?php
namespace BD\Bundle\XmlRpcBundle\Tests\EventListener;

use BD\Bundle\XmlRpcBundle\EventListener\ResponseEventListener;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class BaseEventListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return EventSubscriberInterface
     */
    abstract protected function getEventListener();

    /**
     * @return \Symfony\Component\HttpKernel\HttpKernel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHttpKernelMock()
    {
        if ( !isset( $this->httpKernelMock ) )
        {
            $this->httpKernelMock = $this
                ->getMockBuilder( 'Symfony\Component\HttpKernel\HttpKernel' )
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->httpKernelMock;
    }

    /**
     * @covers ViewEventListener::getSubscribedEvents()
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = $this->getEventListener();
        foreach ( $eventListener::getSubscribedEvents() as $subscribedListeners )
        {
            foreach ( $subscribedListeners as $subscribedListener )
            {
                self::assertTrue(
                    method_exists( $eventListener, $subscribedListener[0] ),
                    "Failed asserting that $subscribedListener[0] is a valid method from the listener"
                );
            }
        }
    }

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpKernelMock;
}
