<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithAlwaysAutoloadedClasses extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Fixture\Container\ContainerWithAlwaysAutoloadedClasses::class => 'WoohooLabs__Zen__Tests__Fixture__Container__ContainerWithAlwaysAutoloadedClasses',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
        include_once $this->rootDirectory . '/tests/Double/StubDefinition.php';
    }
}
