<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Autoload;

interface AutoloadConfigInterface
{
    public function isGlobalAutoloadEnabled(): bool;

    public function getRootDirectory(): string;

    /**
     * @return string[]
     */
    public function getAlwaysAutoloadedClasses(): array;

    /**
     * @return string[]
     */
    public function getExcludedClasses(): array;
}
