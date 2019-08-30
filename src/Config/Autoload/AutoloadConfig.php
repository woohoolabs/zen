<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Autoload;

use function rtrim;

final class AutoloadConfig implements AutoloadConfigInterface
{
    private bool $isGlobalAutoloadEnabled;
    private string $rootDirectory;
    /** @var array<int, string> */
    private array $alwaysAutoloadedClasses;
    /** @var array<int, string> */
    private array $excludedClasses;

    public static function disabledGlobally(string $rootDirectory = ""): AutoloadConfig
    {
        return new AutoloadConfig(false, $rootDirectory);
    }

    public static function enabledGlobally(string $rootDirectory): AutoloadConfig
    {
        return new AutoloadConfig(true, $rootDirectory);
    }

    public static function create(bool $isGlobalAutoloadEnabled, string $rootDirectory = ""): AutoloadConfig
    {
        return new AutoloadConfig($isGlobalAutoloadEnabled, $rootDirectory);
    }

    public function __construct(bool $isGlobalAutoloadEnabled, string $rootDirectory = "")
    {
        $this->isGlobalAutoloadEnabled = $isGlobalAutoloadEnabled;
        $this->setRootDirectory($rootDirectory);
        $this->alwaysAutoloadedClasses = [];
        $this->excludedClasses = [];
    }

    public function isGlobalAutoloadEnabled(): bool
    {
        return $this->isGlobalAutoloadEnabled;
    }

    public function setRootDirectory(string $rootDirectory): AutoloadConfig
    {
        $this->rootDirectory = rtrim($rootDirectory, "\\/");

        return $this;
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    /**
     * @param array<int, string> $alwaysAutoloadedClasses
     */
    public function setAlwaysAutoloadedClasses(array $alwaysAutoloadedClasses): AutoloadConfig
    {
        $this->alwaysAutoloadedClasses = $alwaysAutoloadedClasses;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getAlwaysAutoloadedClasses(): array
    {
        return $this->alwaysAutoloadedClasses;
    }

    /**
     * @param array<int, string> $excludedClasses
     */
    public function setExcludedClasses(array $excludedClasses): AutoloadConfig
    {
        $this->excludedClasses = $excludedClasses;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getExcludedClasses(): array
    {
        return $this->excludedClasses;
    }
}
