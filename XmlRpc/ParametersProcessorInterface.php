<?php
/**
 * File containing the ParametersProcessorInterface class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace BD\Bundle\XmlRpcBundle\XmlRpc;

interface ParametersProcessorInterface
{
    /**
     * Returns route path arguments for the XML-RPC $parameters
     * @param array $parameters
     * @return array array of arguments
     */
    public function getRoutePathArguments( $parameters );

    /**
     * Returns named parameters from the XML-RPC $parameters
     * @param array $parameters
     * @return array hash of parameters
     */
    public function getParameters( $parameters );
}
