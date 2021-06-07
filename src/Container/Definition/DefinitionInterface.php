<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

interface DefinitionInterface
{
    public function getId(string $parentId = ""): string;

    public function getHash(string $parentId = ""): string;

    public function isSingleton(string $parentId = ""): bool;

    public function isEntryPoint(string $parentId = ""): bool;

    public function isFileBased(string $parentId = ""): bool;

    public function increaseReferenceCount(string $parentId, bool $isParentSingleton): DefinitionInterface;

    public function isDefinitionInlinable(string $parentId = ""): bool;

    public function isSingletonCheckEliminable(string $parentId = ""): bool;

    public function needsDependencyResolution(): bool;

    public function resolveDependencies(): DefinitionInterface;

    /**
     * @return string[]
     */
    public function getClassDependencies(): array;

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     */
    public function instantiate($instantiation, $parentId): mixed;

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string;
}
