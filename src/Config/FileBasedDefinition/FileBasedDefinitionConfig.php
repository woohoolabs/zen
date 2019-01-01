<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\FileBasedDefinition;

use function trim;

final class FileBasedDefinitionConfig implements FileBasedDefinitionConfigInterface
{
    /**
     * @var bool
     */
    private $isGlobalFileBasedDefinitionsEnabled;

    /**
     * @var string
     */
    private $relativeDefinitionDirectory;

    /**
     * @var array
     */
    private $excludedClasses;

    public static function disabledGlobally(string $relativeDefinitionDirectory = ""): FileBasedDefinitionConfig
    {
        return new FileBasedDefinitionConfig(false, $relativeDefinitionDirectory);
    }

    public static function enabledGlobally(string $relativeDefinitionDirectory): FileBasedDefinitionConfig
    {
        return new FileBasedDefinitionConfig(true, $relativeDefinitionDirectory);
    }

    public static function create(bool $isGlobalAutoloadEnabled, string $relativeDefinitionDirectory = ""): FileBasedDefinitionConfig
    {
        return new FileBasedDefinitionConfig($isGlobalAutoloadEnabled, $relativeDefinitionDirectory);
    }

    public function __construct(bool $isGlobalAutoloadEnabled, string $relativeDefinitionDirectory = "")
    {
        $this->isGlobalFileBasedDefinitionsEnabled = $isGlobalAutoloadEnabled;
        $this->excludedClasses = [];
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
     * @param string[] $excludedClasses
     */
    public function setExcludedClasses(array $excludedClasses): FileBasedDefinitionConfig
    {
        $this->excludedClasses = $excludedClasses;

        return $this;
    }

    public function getExcludedClasses(): array
    {
        return $this->excludedClasses;
    }
}
