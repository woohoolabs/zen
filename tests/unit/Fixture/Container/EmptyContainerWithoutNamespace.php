<?php

use WoohooLabs\Zen\AbstractCompiledContainer;

class EmptyContainerWithoutNamespace extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \EmptyContainerWithoutNamespace::class => 'EmptyContainerWithoutNamespace',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];
}
