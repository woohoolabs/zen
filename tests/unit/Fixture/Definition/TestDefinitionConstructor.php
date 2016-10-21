<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\ClassEntryPoint;
use WoohooLabs\Dicone\Definition\ContainerConfigInterface;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;

class TestContainerConfigConstructor implements ContainerConfigInterface
{
    public function getEntryPoints(): array
    {
        return [
            new ClassEntryPoint(ConstructorA::class)
        ];
    }

    public function getDefinitionItems(): array
    {
        return [];
    }
}
