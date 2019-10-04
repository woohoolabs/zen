<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

interface PreloadConfigInterface
{
    public function getRelativeBasePath(): string;

    /**
     * @return PreloadInterface[]
     */
    public function getPreloadedClasses(): array;

    /**
     * @return string[]
     */
    public function getPreloadedFiles(): array;
}
