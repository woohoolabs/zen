<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\AbstractDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;

class TestDefinition extends AbstractDefinition
{
    public function __construct(
        string $id,
        string $scope,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        int $referenceCount = 0
    ) {
        parent::__construct($id, $scope, $isEntryPoint, $isAutoloaded, $isFileBased, $referenceCount);
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

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        return "";
    }
}
