<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\ContainerConfigInterface;
use WoohooLabs\Dicone\Definition\Definition;
use WoohooLabs\Dicone\Definition\DirectoryWildcardEntryPoint;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

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
