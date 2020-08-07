<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;

class StubPrototypeContainer extends AbstractCompiledContainer
{
    /**
     * @param string $id
     */
    public function has($id): bool
    {
        return match ($id) {
            'WoohooLabs\Zen\Tests\Double\StubContainerEntry' => true,
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
            'WoohooLabs\Zen\Tests\Double\StubContainerEntry' => new \WoohooLabs\Zen\Tests\Double\StubContainerEntry(),
            default => throw new NotFoundException($id),
        };
    }
}
