<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class EmptyContainerWithNamespace extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Unit\Fixture\Container\EmptyContainerWithNamespace::class => 'WoohooLabs__Zen__Tests__Unit__Fixture__Container__EmptyContainerWithNamespace',
        \Psr\Container\ContainerInterface::class => 'Psr__Container__ContainerInterface',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }
}
