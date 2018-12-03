<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

interface DefinitionInterface
{
    public function getId(string $parentId): string;

    public function getHash(string $parentId): string;

    public function getScope(string $parentId): string;

    public function isAutoloaded(): bool;

    public function needsDependencyResolution(): bool;

    public function resolveDependencies(): DefinitionInterface;

    /**
     * @return string[]
     */
    public function getClassDependencies(): array;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string;
}
