<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\AbstractContainerConfig;

class DummyCompilerConfig extends AbstractCompilerConfig
{
    public function getContainerNamespace(): string
    {
        return "";
    }

    public function getContainerClassName(): string
    {
        return "";
    }

    public function useConstructorInjection(): bool
    {
        return false;
    }

    public function usePropertyInjection(): bool
    {
        return false;
    }

    /**
     * @return AbstractContainerConfig[]
     */
    public function getContainerConfigs(): array
    {
        return [];
    }
}
