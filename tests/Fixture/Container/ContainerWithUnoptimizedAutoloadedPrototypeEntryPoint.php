<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithUnoptimizedAutoloadedPrototypeEntryPoint extends AbstractCompiledContainer
{
    /**
     * @var string[]
     */
    protected static $entryPoints = [
        'WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition' => '_proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition',
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
        include_once $this->rootDirectory . '/src/Container/Definition/DefinitionInterface.php';
        include_once $this->rootDirectory . '/src/Container/Definition/AbstractDefinition.php';
        include_once $this->rootDirectory . '/tests/Double/TestDefinition.php';
        include_once $this->rootDirectory . '/tests/Double/StubPrototypeDefinition.php';

        self::$entryPoints['WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition'] = 'WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition';

        return $this->WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition();
    }

    public function WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition()
    {
        // This is a dummy definition.
    }
}
