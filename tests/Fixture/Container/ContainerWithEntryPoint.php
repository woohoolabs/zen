<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        'WoohooLabs\Zen\Tests\Double\StubSingletonDefinition' => 'WoohooLabs__Zen__Tests__Double__StubSingletonDefinition',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function WoohooLabs__Zen__Tests__Double__StubSingletonDefinition()
    {
        // This is a dummy definition.
    }
}
