<?php

namespace BD\Bundle\XmlRpcBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BDXmlRpcBundle extends Bundle
{
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        $container->addCompilerPass( new DependencyInjection\Compiler\ParametersProviderCompilerPass() );
    }

}
