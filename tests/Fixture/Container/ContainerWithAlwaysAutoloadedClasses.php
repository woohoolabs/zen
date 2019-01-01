<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithAlwaysAutoloadedClasses extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
        include_once $this->rootDirectory . '/tests/Double/StubSingletonDefinition.php';
    }
}
