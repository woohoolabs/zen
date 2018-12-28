<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

interface DefinitionInterface
{
    public function getId(string $parentId): string;

    public function getHash(string $parentId): string;

    public function isSingleton(string $parentId): bool;

    public function isEntryPoint(): bool;

    public function isAutoloaded(): bool;

    public function isFileBased(): bool;

    public function getReferenceCount(): int;

    public function increaseReferenceCount(): void;

    public function needsDependencyResolution(): bool;

    public function resolveDependencies(): DefinitionInterface;

    /**
     * @return string[]
     */
    public function getClassDependencies(): array;

    public function compile(DefinitionCompilation $compilation): string;
}
