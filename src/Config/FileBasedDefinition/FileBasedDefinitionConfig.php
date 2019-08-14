<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\FileBasedDefinition;

use function trim;

final class FileBasedDefinitionConfig implements FileBasedDefinitionConfigInterface
{
    private bool $isGlobalFileBasedDefinitionsEnabled;

    private string $relativeDefinitionDirectory;

    private array $excludedDefinitions;

    public static function disabledGlobally(string $relativeDefinitionDirectory = ""): FileBasedDefinitionConfig
    {
        return new FileBasedDefinitionConfig(false, $relativeDefinitionDirectory);
    }

    public static function enabledGlobally(string $relativeDefinitionDirectory): FileBasedDefinitionConfig
    {
        return new FileBasedDefinitionConfig(true, $relativeDefinitionDirectory);
    }

    public static function create(
        bool $isGlobalFileBasedDefinitionsEnabled,
        string $relativeDefinitionDirectory = ""
    ): FileBasedDefinitionConfig {
        return new FileBasedDefinitionConfig($isGlobalFileBasedDefinitionsEnabled, $relativeDefinitionDirectory);
    }

    public function __construct(bool $isGlobalFileBasedDefinitionsEnabled, string $relativeDefinitionDirectory = "")
    {
        $this->isGlobalFileBasedDefinitionsEnabled = $isGlobalFileBasedDefinitionsEnabled;
        $this->excludedDefinitions = [];
        $this->setRelativeDefinitionDirectory($relativeDefinitionDirectory);
    }

    public function isGlobalFileBasedDefinitionEnabled(): bool
    {
        return $this->isGlobalFileBasedDefinitionsEnabled;
    }

    public function setRelativeDefinitionDirectory(string $relativeDefinitionDirectory): FileBasedDefinitionConfig
    {
        $this->relativeDefinitionDirectory = trim($relativeDefinitionDirectory, "\\/");

        return $this;
    }

    public function getRelativeDefinitionDirectory(): string
    {
        return $this->relativeDefinitionDirectory;
    }

    /**
     * @param string[] $excludedDefinitions
     */
    public function setExcludedDefinitions(array $excludedDefinitions): FileBasedDefinitionConfig
    {
        $this->excludedDefinitions = $excludedDefinitions;

        return $this;
    }

    public function getExcludedDefinitions(): array
    {
        return $this->excludedDefinitions;
    }
}
