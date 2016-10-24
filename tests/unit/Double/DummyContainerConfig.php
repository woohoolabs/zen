<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;

class DummyContainerConfig extends AbstractContainerConfig
{
    protected function getEntryPoints(): array
    {
        return [];
    }

    protected function getDefinitionHints(): array
    {
        return [];
    }

    protected function getWildcardHints(): array
    {
        return [];
    }
}
