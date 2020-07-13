<?php

declare(strict_types=1);

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;

class EmptyContainerWithoutNamespace extends AbstractCompiledContainer
{
    protected string $rootDirectory;

    public function __construct(string $rootDirectory = "")
    {
        $this->rootDirectory = $rootDirectory;
    }

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
