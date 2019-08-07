<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

final class PreloadConfig implements PreloadConfigInterface
{
    /**
     * @var PreloadInterface[]
     */
    private $preloads;

    public static function create(): PreloadConfig
    {
        return new PreloadConfig();
    }

    public function __construct()
    {
        $this->preloads = [];
    }

    /**
     * @param PreloadInterface[] $preloads
     */
    public function setPreloads(array $preloads): PreloadConfig
    {
        $this->preloads = $preloads;

        return $this;
    }

    /**
     * @return PreloadInterface[]
     */
    public function getPreloads(): array
    {
        return $this->preloads;
    }
}
