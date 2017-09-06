<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithEntryPoint::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__ContainerWithEntryPoint',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
        \WoohooLabs\Zen\Tests\Unit\Double\StubDefinition::class => 'WoohooLabs__Zen__Tests__Unit__Double__StubDefinition',
    ];

    public function WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
