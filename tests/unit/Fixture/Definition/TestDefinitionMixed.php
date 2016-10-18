<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Definition;

use WoohooLabs\Dicone\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Definition\DirectoryWildcardEntrypoint;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

class TestDefinitionMixed implements DefinitionInterface
{
    public function getEntryPoints(): array
    {
        return [
            new DirectoryWildcardEntrypoint(__DIR__ . "/../DependencyGraph/Container/Entrypoint")
        ];
    }

    public function getDefinitionItems(): array
    {
        return [
            ClassC::class => DefinitionItem::prototype(ClassC::class)
        ];
    }
}
