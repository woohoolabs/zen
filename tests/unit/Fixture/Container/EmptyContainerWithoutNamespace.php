<?php

use WoohooLabs\Zen\AbstractContainer;

class EmptyContainerWithoutNamespace extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \EmptyContainerWithoutNamespace::class => 'EmptyContainerWithoutNamespace',
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
    ];
}
