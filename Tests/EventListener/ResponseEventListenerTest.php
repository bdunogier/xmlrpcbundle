<?php
namespace BD\Bundle\XmlRpcBundle\Tests\EventListener;

use BD\Bundle\XmlRpcBundle\EventListener\ResponseEventListener;
use BD\Bundle\XmlRpcBundle\XmlRpc\Response as XmlRpcResponse;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseEventListenerTest extends BaseEventListenerTest
{
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
        $xmlRpcResponse = new XmlRpcResponse( 1 );
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
     * @return \BD\Bundle\XmlRpcBundle\EventListener\ResponseEventListener
     */
    protected function getEventListener()
    {
        return new ResponseEventListener(
            $this->getResponseGeneratorMock()
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseGeneratorMock()
    {
        if ( !isset( $this->responseGeneratorMock ) )
        {
            $this->responseGeneratorMock = $this
                ->getMockBuilder( 'BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGeneratorInterface' )
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $this->responseGeneratorMock;
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\ResponseGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseGeneratorMock;
}

