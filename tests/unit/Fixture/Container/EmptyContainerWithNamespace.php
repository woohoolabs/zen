<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class EmptyContainerWithNamespace extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\EmptyContainerWithNamespace::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__EmptyContainerWithNamespace',
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
    ];
}
