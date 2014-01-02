<?php
namespace BD\Bundle\XmlRpcBundle\Tests\EventListener;

use BD\Bundle\XmlRpcBundle\EventListener\RequestEventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Routing\RequestContext;

class RequestEventListenerTest extends BaseEventListenerTest
{
    /**
     * @covers RequestEventListener::onKernelRequest()
     */
    public function testOnKernelRequestSubRequest()
    {
        $event = new GetResponseEvent(
            $this->getHttpKernelMock(),
            new Request(),
            HttpKernelInterface::SUB_REQUEST
        );

        $this->getRequestGeneratorMock()
            ->expects( $this->never() )
            ->method( 'generateFromRequest' );

        self::assertNull(
            $this->getEventListener()->onKernelRequest( $event )
        );
    }

    /**
     * @covers RequestEventListener::onKernelRequest()
     */
    public function testOnKernelRequestNotXmlRpcRequest()
    {
        $event = new GetResponseEvent(
            $this->getHttpKernelMock(),
            new Request(
                array(), array(), array(), array(), array(), array( 'path' => '/anything' )
            ),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->getRequestGeneratorMock()
            ->expects( $this->never() )
            ->method( 'generateFromRequest' );

        self::assertNull(
            $this->getEventListener()->onKernelRequest( $event )
        );
    }

    public function getHttpVerbs()
    {
        return array(
            array( 'GET' ),
            array( 'PUT' ),
            array( 'DELETE' ),
            array( 'OPTIONS' ),
            array( 'ANYTHING' )
        );
    }

    /**
     * @covers RequestEventListener::onKernelRequest()
     */
    public function testOnKernelRequest()
    {
        $httpRequest = Request::create( '/xmlrpc.php', 'POST' );
        $xmlRpcRequest = Request::create( '/xmlrpc/methodName', 'POST' );

        $event = new GetResponseEvent(
            $this->getHttpKernelMock(),
            $httpRequest,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->getRequestGeneratorMock()
            ->expects( $this->once() )
            ->method( 'generateFromRequest' )
            ->with( $httpRequest )
            ->will( $this->returnValue( $xmlRpcRequest ) );

        $this->getRouterMock()
            ->expects( $this->once() )
            ->method( 'getContext' )
            ->will( $this->returnValue( new RequestContext() ) );

        $this->getHttpKernelMock()
            ->expects( $this->once() )
            ->method( 'handle' )
            ->will( $this->returnValue( new Response() ) );

        self::assertNull(
            $this->getEventListener()->onKernelRequest( $event )
        );
    }

    /**
     * @dataProvider getHttpVerbs()
     * @covers RequestEventListener::onKernelRequest()
     */
    public function testOnKernelRequestNotPostRequest( $verb )
    {
        $event = new GetResponseEvent(
            $this->getHttpKernelMock(),
            Request::create( '/xmlrpc2', 'GET' ),
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->getRequestGeneratorMock()
            ->expects( $this->never() )
            ->method( 'generateFromRequest' );

        self::assertNull(
            $this->getEventListener()->onKernelRequest( $event )
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\EventListener\RequestEventListener
     */
    protected function getEventListener()
    {
        return new RequestEventListener(
            $this->getHttpKernelMock(),
            $this->getRouterMock(),
            $this->getRequestGeneratorMock()
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestGeneratorMock()
    {
        if ( !isset( $this->requestGeneratorMock ) )
        {
            $this->requestGeneratorMock = $this->getMock( 'BD\Bundle\XmlRpcBundle\XmlRpc\RequestGeneratorInterface' );
        }
        return $this->requestGeneratorMock;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRouterMock()
    {
        if ( !isset( $this->routerMock ) )
        {
            $this->routerMock = $this->getMock( 'Symfony\Component\Routing\RouterInterface' );
        }
        return $this->routerMock;
    }

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestGeneratorMock;

    /**
     * @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
}
