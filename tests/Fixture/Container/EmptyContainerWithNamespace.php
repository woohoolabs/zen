<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;

class EmptyContainerWithNamespace extends AbstractCompiledContainer
{
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return match ($id) {
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
            default => throw new NotFoundException($id),
        };
    }
}
