<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class ContainerWithEntry extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithEntry::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__ContainerWithEntry',
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
    ];

    protected function WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
