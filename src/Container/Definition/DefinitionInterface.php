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

    public function isAutoloaded(string $parentId = ""): bool;

    public function isFileBased(string $parentId = ""): bool;

    public function increaseReferenceCount(string $parentId, bool $isParentSingleton): DefinitionInterface;

    public function isAutoloadingInlinable(string $parentId = "", bool $inline = false): bool;

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
     * @return mixed
     */
    public function instantiate($instantiation, $parentId);

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string;
}
