<?php
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

use Symfony\Component\HttpFoundation\Request;

interface RequestGeneratorInterface
{
    /**
     * Generates an internal XML RPC request from an HTTP one
     * @param Request $originalRequest
     * @return Request
     */
    public function generateFromRequest( Request $originalRequest );
}
