<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;

class ContainerWithEntry extends AbstractContainer
{
    /**
     * @var string[]
     */
    protected $entryPoints = [
        \Interop\Container\ContainerInterface::class => 'Interop__Container__ContainerInterface',
    ];

    protected function WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
