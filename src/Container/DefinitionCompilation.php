<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

final class DefinitionCompilation
{
    /**
     * @var AutoloadConfigInterface
     */
    private $autoloadConfig;

    /**
     * @var FileBasedDefinitionConfigInterface
     */
    private $fileBasedDefinitionConfig;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(
        AutoloadConfigInterface $autoloadConfig,
        FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig,
        array $definitions
    ) {
        $this->autoloadConfig = $autoloadConfig;
        $this->fileBasedDefinitionConfig = $fileBasedDefinitionConfig;
        $this->definitions = $definitions;
    }

    public function getAutoloadConfig(): AutoloadConfigInterface
    {
        return $this->autoloadConfig;
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
