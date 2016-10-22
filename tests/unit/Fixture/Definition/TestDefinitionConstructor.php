<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\Definition;

use WoohooLabs\Zen\Definition\ClassEntryPoint;
use WoohooLabs\Zen\Definition\ContainerConfigInterface;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;

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
