<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithAutoloadedEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithAutoloadedEntryPoint::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__ContainerWithAutoloadedEntryPoint',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
        \WoohooLabs\Zen\Tests\Unit\Double\StubDefinition::class => '_proxy__WoohooLabs__Zen__Tests__Unit__Double__StubDefinition',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function _proxy__WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        include_once $this->rootDirectory . '/tests/unit/Double/StubDefinition.php';

        self::$entryPoints[\WoohooLabs\Zen\Tests\Unit\Double\StubDefinition::class] = 'WoohooLabs__Zen__Tests__Unit__Double__StubDefinition';

        return $this->WoohooLabs__Zen__Tests__Unit__Double__StubDefinition();
    }

    public function WoohooLabs__Zen__Tests__Unit__Double__StubDefinition()
    {
        // This is a dummy definition.
    }
}
