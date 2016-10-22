<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\Definition;

use WoohooLabs\Zen\Definition\ContainerConfigInterface;
use WoohooLabs\Zen\Definition\Definition;
use WoohooLabs\Zen\Definition\DirectoryWildcardEntryPoint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

class TestContainerConfigMixed implements ContainerConfigInterface
{
    public function getEntryPoints(): array
    {
        return [
            new DirectoryWildcardEntryPoint(__DIR__ . "/../DependencyGraph/Container/Entrypoint")
        ];
    }

    public function getDefinitionItems(): array
    {
        return [
            ClassC::class => Definition::prototype(ClassC::class)
        ];
    }
}
