<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\AbstractDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

class TestDefinition extends AbstractDefinition
{
    private bool $definitionInlinable;

    public function __construct(
        string $id,
        bool $isSingleton = true,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0,
        bool $isDefinitionInlinable = false
    ) {
        parent::__construct($id, $isSingleton, $isEntryPoint, $isFileBased, $singletonReferenceCount, $prototypeReferenceCount);
        $this->definitionInlinable = $isDefinitionInlinable;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassDependencies(): array
    {
        return [];
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     */
    public function instantiate($instantiation, $parentId): mixed
    {
        return null;
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string {
        return "";
    }

    public function isDefinitionInlinable(string $parentId = ""): bool
    {
        return $this->definitionInlinable;
    }
}
