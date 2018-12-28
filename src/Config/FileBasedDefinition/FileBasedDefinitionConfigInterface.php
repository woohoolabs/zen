<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\FileBasedDefinition;

interface FileBasedDefinitionConfigInterface
{
    public function isGlobalFileBasedDefinitionEnabled(): bool;

    public function getRelativeDirectory(): string;

    /**
     * @return string[]
     */
    public function getExcludedClasses(): array;
}
