<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\EntryPoint;

use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;

abstract class AbstractEntryPoint implements EntryPointInterface
{
    private ?bool $fileBased;

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
