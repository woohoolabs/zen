<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;

class ContainerWithFileBasedEntryPoint extends AbstractCompiledContainer
{
    /** @var string[] */
    protected static array $entryPoints = [
        'WoohooLabs\Zen\Tests\Double\StubSingletonDefinition' => 'WoohooLabs__Zen__Tests__Double__StubSingletonDefinition',
    ];
    protected string $rootDirectory;

    public function __construct(string $rootDirectory = "")
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function WoohooLabs__Zen__Tests__Double__StubSingletonDefinition()
    {
        return require __DIR__ . '/Definitions/WoohooLabs__Zen__Tests__Double__StubSingletonDefinition.php';
    }
}
