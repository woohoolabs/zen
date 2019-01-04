<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

interface DefinitionInterface
{
    public function getId(string $parentId = ""): string;

    public function getHash(string $parentId = ""): string;

    public function isSingleton(string $parentId = ""): bool;

    public function isEntryPoint(string $parentId = ""): bool;

    public function isAutoloaded(string $parentId = ""): bool;

    public function isFileBased(string $parentId = ""): bool;

    public function getSingletonReferenceCount(string $parentId = ""): int;

    public function getPrototypeReferenceCount(string $parentId = ""): int;

    public function increaseReferenceCount(string $parentId, bool $isParentSingleton): DefinitionInterface;

    public function needsDependencyResolution(): bool;

    public function resolveDependencies(): DefinitionInterface;

    /**
     * @return string[]
     */
    public function getClassDependencies(): array;

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string;
}
