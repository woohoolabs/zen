<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\FileBasedDefinition;

interface FileBasedDefinitionConfigInterface
{
    public function isGlobalFileBasedDefinitionEnabled(): bool;

    public function getRelativeDefinitionDirectory(): string;

    /**
     * @return string[]
     */
    public function getExcludedDefinitions(): array;
}
