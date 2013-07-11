<?php
/**
 * File containing the RequestEventListener class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace BD\Bundle\XmlRpcBundle\Tests\EventListener;

use BD\Bundle\XmlRpcBundle\EventListener\RequestEventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Routing\RequestContext;

class RequestEventListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers RequestEventListener::getSubscribedEvents()
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(
                KernelEvents::REQUEST => array(
                    array( 'onKernelRequest', 16 ),
                )
            ),
            RequestEventListener::getSubscribedEvents()
        );
    }

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
            $this->getRequestEventListener()->onKernelRequest( $event )
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
            $this->getRequestEventListener()->onKernelRequest( $event )
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
            $this->getRequestEventListener()->onKernelRequest( $event )
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
            $this->getRequestEventListener()->onKernelRequest( $event )
        );
    }

    /**
     * @return \BD\Bundle\XmlRpcBundle\EventListener\RequestEventListener
     */
    private function getRequestEventListener()
    {
        return new RequestEventListener(
            $this->getHttpKernelMock(),
            $this->getRouterMock(),
            $this->getRequestGeneratorMock()
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
     * @return \BD\Bundle\XmlRpcBundle\XmlRpc\RequestParser|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getRequestGeneratorMock()
    {
        if ( !isset( $this->requestGeneratorMock ) )
        {
            $this->requestGeneratorMock = $this
                ->getMockBuilder( 'BD\\Bundle\\XmlRpcBundle\\XmlRpc\\RequestGenerator' )
                ->disableOriginalConstructor()
                ->getMock();
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
            $this->routerMock = $this->getMock( 'Symfony\\Component\\Routing\\RouterInterface' );
        }
        return $this->routerMock;
    }

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpKernelMock;

    /**
     * @var \BD\Bundle\XmlRpcBundle\XmlRpc\RequestGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestGeneratorMock;

    /**
     * @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $routerMock;
}
