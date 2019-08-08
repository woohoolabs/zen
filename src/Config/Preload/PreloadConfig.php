<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

final class PreloadConfig implements PreloadConfigInterface
{
    /**
     * @var PreloadInterface[]
     */
    private $preloadedClasses;

    /**
     * @var string[]
     */
    private $preloadedFiles;

    public static function create(): PreloadConfig
    {
        return new PreloadConfig();
    }

    public function __construct()
    {
        $this->preloadedClasses = [];
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
