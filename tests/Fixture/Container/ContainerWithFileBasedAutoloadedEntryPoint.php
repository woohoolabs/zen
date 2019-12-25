<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithFileBasedAutoloadedEntryPoint extends AbstractCompiledContainer
{
    protected static array $entryPoints = [
        'WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition' => '_proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition',
    ];
    protected string $rootDirectory;

    public function __construct(string $rootDirectory = "")
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
