<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithAutoloadedEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Fixture\Container\ContainerWithAutoloadedEntryPoint::class => 'WoohooLabs__Zen__Tests__Fixture__Container__ContainerWithAutoloadedEntryPoint',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
        \WoohooLabs\Zen\Tests\Double\StubDefinition::class => '_proxy__WoohooLabs__Zen__Tests__Double__StubDefinition',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function _proxy__WoohooLabs__Zen__Tests__Double__StubDefinition()
    {
        include_once $this->rootDirectory . '/tests/Double/StubDefinition.php';

        self::$entryPoints[\WoohooLabs\Zen\Tests\Double\StubDefinition::class] = 'WoohooLabs__Zen__Tests__Double__StubDefinition';

        return $this->WoohooLabs__Zen__Tests__Double__StubDefinition();
    }

    public function WoohooLabs__Zen__Tests__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
