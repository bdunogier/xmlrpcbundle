<?php
/**
 * File containing the Hello controller.
 */
namespace BD\Bundle\XmlRpcBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use BD\Bundle\XmlRpcBundle\XmlRpc\Response;

class Hello extends Controller
{
    public function hello()
    {
        $parameters = $this->getRequest()->request->all();

        // we return a hash
        $response = array( 'greetings' => "Hello " . array_shift( $parameters ) );

        // we add other parameters to the response array, as is
        if ( count( $parameters ) > 0 )
            $response = array_merge( $response, $parameters );

        // @todo this is stupid, we don't need a response object, just return whatever you need to
        return new Response( $response );
    }
}
