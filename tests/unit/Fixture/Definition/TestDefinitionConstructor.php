<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\ClassEntrypoint;
use WoohooLabs\Dicone\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;

class TestDefinitionConstructor implements DefinitionInterface
{
    public function getEntryPoints(): array
    {
        return [
            new ClassEntrypoint(ConstructorA::class)
        ];
    }

    public function getDefinitionItems(): array
    {
        return [];
    }
}
