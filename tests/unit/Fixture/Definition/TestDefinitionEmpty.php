<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\DefinitionInterface;

class TestDefinitionEmpty implements DefinitionInterface
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
