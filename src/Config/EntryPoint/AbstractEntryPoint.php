<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;

abstract class AbstractEntryPoint implements EntryPointInterface
{
    private ?bool $autoloaded;
    private ?bool $fileBased;

    public function autoload(): static
    {
        $this->autoloaded = true;

        return $this;
    }

    public function disableAutoload(): static
    {
        $this->autoloaded = false;

        return $this;
    }

    /**
     * @internal
     */
    public function isAutoloaded(AutoloadConfigInterface $autoloadConfig): bool
    {
        return $this->autoloaded ?? $autoloadConfig->isGlobalAutoloadEnabled();
    }

    public function fileBased(): static
    {
        $this->fileBased = true;

        return $this;
    }

    public function disableFileBased(): static
    {
        $this->fileBased = false;

        return $this;
    }

    /**
     * @internal
     */
    public function isFileBased(FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig): bool
    {
        return $this->fileBased ?? $fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled();
    }
}
