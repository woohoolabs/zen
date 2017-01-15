<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class EmptyContainerWithNamespace extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\EmptyContainerWithNamespace::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__EmptyContainerWithNamespace',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];
}
