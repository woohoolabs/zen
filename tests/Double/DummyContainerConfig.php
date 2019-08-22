<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;

class DummyContainerConfig extends AbstractContainerConfig
{
    /**
     * @return EntryPointInterface[]|string[]
     */
    protected function getEntryPoints(): array
    {
        return [];
    }

    /**
     * @return DefinitionHintInterface[]|string[]
     */
    protected function getDefinitionHints(): array
    {
        return [];
    }

    /**
     * @return WildcardHintInterface[]
     */
    protected function getWildcardHints(): array
    {
        return [];
    }
}
