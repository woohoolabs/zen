<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class ContainerWithEntryPoint extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithEntryPoint::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__ContainerWithEntryPoint',
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
        \WoohooLabs\Zen\Tests\Unit\Double\StubDefinition::class => 'WoohooLabs__Zen__Tests__Unit__Double__StubDefinition',
    ];

    protected function WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
