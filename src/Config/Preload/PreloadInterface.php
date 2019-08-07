<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Preload;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;

interface PreloadInterface
{
    /**
     * @return string[]
     * @internal
     */
    public function getClassNames(): array;
}
