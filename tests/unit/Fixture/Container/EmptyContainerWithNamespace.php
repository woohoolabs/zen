<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class EmptyContainerWithNamespace extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
    ];
}
