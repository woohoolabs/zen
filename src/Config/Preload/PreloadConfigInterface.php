<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

interface PreloadConfigInterface
{
    /**
     * @return PreloadInterface[]
     */
    public function getPreloads(): array;
}
