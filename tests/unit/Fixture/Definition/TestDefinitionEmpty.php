<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\ContainerConfigInterface;

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
