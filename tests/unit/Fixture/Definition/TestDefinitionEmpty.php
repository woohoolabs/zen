<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\Definition;

use WoohooLabs\Zen\Definition\ContainerConfigInterface;

class TestContainerConfigEmpty implements ContainerConfigInterface
{
    public function getEntryPoints(): array
    {
        return [];
    }

    public function getDefinitionItems(): array
    {
        return [];
    }
}
