<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\AbstractDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

class TestDefinition extends AbstractDefinition
{
    public function __construct(
        string $id,
        string $scope,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ) {
        parent::__construct($id, $scope, $isEntryPoint, $isAutoloaded, $isFileBased, $singletonReferenceCount, $prototypeReferenceCount);
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function getClassDependencies(): array
    {
        return [];
    }

    public function instantiate(DefinitionInstantiation $instantiation, string $parentId)
    {
        return null;
    }

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        return "";
    }
}
