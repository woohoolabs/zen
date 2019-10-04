<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use function rtrim;

final class PreloadConfig implements PreloadConfigInterface
{
    private string $relativeBasePath;
    /** @var PreloadInterface[] */
    private array $preloadedClasses;
    /** @var string[] */
    private array $preloadedFiles;

    public static function create(string $relativeBasePath = ""): PreloadConfig
    {
        return new PreloadConfig($relativeBasePath);
    }

    public function __construct(string $relativeBasePath = "")
    {
        $this->setRelativeBasePath($relativeBasePath);
        $this->preloadedClasses = [];
        $this->preloadedFiles = [];
    }

    public function getRelativeBasePath(): string
    {
        return $this->relativeBasePath;
    }

    public function setRelativeBasePath(string $relativeBasePath): PreloadConfig
    {
        $this->relativeBasePath = rtrim($relativeBasePath, "\\/");

        return $this;
    }

    /**
     * @return PreloadInterface[]
     */
    public function getPreloadedClasses(): array
    {
        return $this->preloadedClasses;
    }

    /**
     * @param PreloadInterface[] $preloadedClasses
     */
    public function setPreloadedClasses(array $preloadedClasses): PreloadConfig
    {
        $this->preloadedClasses = $preloadedClasses;

        return $this;
    }

    /**
     * @param string[] $preloadedFiles
     */
    public function setPreloadedFiles(array $preloadedFiles): PreloadConfig
    {
        $this->preloadedFiles = $preloadedFiles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPreloadedFiles(): array
    {
        return $this->preloadedFiles;
    }
}
