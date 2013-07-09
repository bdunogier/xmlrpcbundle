<?php
/**
 * File containing the Response class.
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

class Response
{
    public function __construct( $return )
    {
        $this->return = $return;
    }

    public $return;
}
