<?php
/**
 * File containing the ParametersProviderCompilerPass class.
 *
 * @copyright Copyright (C) 2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */
namespace BD\Bundle\XmlRpcBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiled the DI tag bd_xmlrpc.parameters_provider
 */
class ParametersProviderCompilerPass implements CompilerPassInterface
{
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'bdxmlrpc.request_generator' ) )
        {
            return;
        }

        $resolverDefinition = $container->getDefinition( 'bdxmlrpc.request_generator' );

        $providers = array();
        $taggedServices = $container->findTaggedServiceIds( 'bdxmlrpc.parameters_processor' );
        foreach ( $taggedServices as $taggedServiceId => $tagAttributes )
        {
            foreach ( $tagAttributes as $attribute )
            {
                if ( !isset( $attribute['methodName'] ) )
                {
                    throw new InvalidArgumentException(
                        "Missing mandatory attribute 'methodName' for tag bdxmlrpc.parameters_processor"
                    );
                }
                $providers[$attribute['methodName']] = new Reference( $taggedServiceId );
            }
        }

        if ( count( $providers ) > 0 )
        {
            $resolverDefinition->setArguments(
                array( $providers )
            );
        }
    }

}
