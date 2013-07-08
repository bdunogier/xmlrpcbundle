<?php
/**
 * File containing the ViewEventListener class.
 */

namespace BD\Bundle\XmlRpcBundle\Tests\EventListener;

use BD\Bundle\XmlRpcBundle\EventListener\ViewEventListener;
use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPUnit_Framework_TestCase;

class ViewEventListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ViewEventListener::getSubscribedEvents()
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(
                KernelEvents::VIEW => array(
                    array( 'onControllerView', 16 ),
                )
            ),
            ViewEventListener::getSubscribedEvents()
        );
    }

    /**
     * @covers ViewEventListener::onControllerView()
     */
    public function testOnControllerViewNotXmlRpcResponse()
    {
        $event = new GetResponseForControllerResultEvent(
            $this->getHttpKernelMock(),
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass()
        );

        $this->getResponseGeneratorMock()
            ->expects( $this->never() )
            ->method( 'fromXmlRpcResponse' );

        self::assertNull(
            $this->getEventListener()->onControllerView( $event )
        );
    }

    /**
     * @covers ViewEventListener::onControllerView()
     */
    public function testOnControllerView()
    {
        $xmlRpcResponse = new XmlRpcResponse();
        $httpResponse = new Response();

        $event = new GetResponseForControllerResultEvent(
            $this->getHttpKernelMock(),
            new Request,
            HttpKernelInterface::MASTER_REQUEST,
            $xmlRpcResponse
        );

        $this->getResponseGeneratorMock()
            ->expects( $this->once() )
            ->method( 'fromXmlRpcResponse' )
            ->with( $xmlRpcResponse )
            ->will( $this->returnValue( $httpResponse ) );

        self::assertNull(
            $this->getEventListener()->onControllerView( $event )
        );

        self::assertEquals(
            $httpResponse,
            $event->getResponse()
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\EventListener\ViewEventListener
     */
    private function getEventListener()
    {
        return new ViewEventListener(
            $this->getResponseGeneratorMock()
        );
    }

    /**
     * @return \Symfony\Component\HttpKernel\HttpKernel|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getHttpKernelMock()
    {
        if ( !isset( $this->httpKernelMock ) )
        {
            $this->httpKernelMock = $this
                ->getMockBuilder( 'Symfony\\Component\\HttpKernel\\HttpKernel' )
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->httpKernelMock;
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseGeneratorMock()
    {
        if ( !isset( $this->responseGeneratorMock ) )
        {
            $this->responseGeneratorMock = $this
                ->getMockBuilder( 'BD\\Bundle\\XmlRpcBundle\\XmlRpc\\ResponseGenerator' )
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $this->responseGeneratorMock;
    }

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpKernelMock;

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseGeneratorMock;
}

