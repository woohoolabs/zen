<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use stdClass;
use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\Tests\Double\StubContainerEntry;

class ContainerWithInjectedProperty extends AbstractCompiledContainer
{
    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return match ($id) {
            'A' => true,
            default => false,
        };
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        return $this->singletonEntries[$id] ?? match ($id) {
            'A' => $this->A(),
            default => throw new NotFoundException($id),
        };
    }

    public function A()
    {
        return true;
    }

    public function getProperty(): stdClass
    {
        $entry = new StubContainerEntry();
        $this->setClassProperties($entry, ['a' => $this->singletonEntries['A'] ?? $this->A()]);

        return $entry->getA();
    }
}
