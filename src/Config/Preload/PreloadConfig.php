<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

final class PreloadConfig implements PreloadConfigInterface
{
    private string $relativeBasePath;

    /**
     * @var PreloadInterface[]
     */
    private array $preloadedClasses;

    /**
     * @var string[]
     */
    private array $preloadedFiles;

    public static function create(string $relativeBasePath = ""): PreloadConfig
    {
        return new PreloadConfig($relativeBasePath);
    }

    public function __construct(string $relativeBasePath = "")
    {
        $this->relativeBasePath = $relativeBasePath;
        $this->preloadedFiles = [];
        $this->preloadedClasses = [];
    }

    public function setRelativeBasePath(string $relativeBasePath): PreloadConfig
    {
        $this->relativeBasePath = $relativeBasePath;

        return $this;
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

    public function getRelativeBasePath(): string
    {
        return $this->relativeBasePath;
    }

    /**
     * @return PreloadInterface[]
     */
    public function getPreloadedClasses(): array
    {
        return $this->preloadedClasses;
    }

    /**
     * @return string[]
     */
    public function getPreloadedFiles(): array
    {
        return $this->preloadedFiles;
    }
}
