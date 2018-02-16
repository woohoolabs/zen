<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithAlwaysAutoloadedClasses extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithAlwaysAutoloadedClasses::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__ContainerWithAlwaysAutoloadedClasses',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
        include_once $this->rootDirectory . '/tests/unit/Double/StubDefinition.php';
    }
}
