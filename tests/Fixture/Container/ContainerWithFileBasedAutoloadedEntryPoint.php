<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithFileBasedAutoloadedEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        \WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition::class => '_proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition',
    ];

    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function _proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition()
    {
        return require __DIR__ . '/Definitions/_proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition.php';
    }

    public function WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition()
    {
        return require __DIR__ . '/Definitions/WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition.php';
    }
}
