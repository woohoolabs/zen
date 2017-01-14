<?php

use WoohooLabs\Zen\AbstractContainer;

class EmptyContainerWithoutNamespace extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \EmptyContainerWithoutNamespace::class => 'EmptyContainerWithoutNamespace',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];
}
