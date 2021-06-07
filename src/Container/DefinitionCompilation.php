<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

final class DefinitionCompilation
{
    private FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig;
    /** @var DefinitionInterface[] */
    private array $definitions;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(
        FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig,
        array $definitions
    ) {
        $this->fileBasedDefinitionConfig = $fileBasedDefinitionConfig;
        $this->definitions = $definitions;
    }

    public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
    {
        return $this->fileBasedDefinitionConfig;
    }

    /**
     * @return DefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getDefinition(string $id): DefinitionInterface
    {
        return $this->definitions[$id];
    }
}
