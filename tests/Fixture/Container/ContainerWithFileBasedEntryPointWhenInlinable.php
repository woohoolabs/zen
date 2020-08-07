<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;

class ContainerWithFileBasedEntryPointWhenInlinable extends AbstractCompiledContainer
{
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return match ($id) {
            'WoohooLabs\Zen\Tests\Double\StubSingletonDefinition' => true,
            default => false,
        };
    }

    /**
     * @param string $id
     * @throws NotFoundException
     */
    public function get($id): mixed
    {
        return $this->singletonEntries[$id] ?? match ($id) {
            'WoohooLabs\Zen\Tests\Double\StubSingletonDefinition' => require __DIR__ . '/Definitions/WoohooLabs__Zen__Tests__Double__StubSingletonDefinition.php',
            default => throw new NotFoundException($id),
        };
    }
}
