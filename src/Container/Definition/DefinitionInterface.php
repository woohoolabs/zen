<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

interface DefinitionInterface
{
    public function getId(): string;

    public function getHash(): string;

    public function needsDependencyResolution(): bool;

    public function isAutoloaded(): bool;

    public function resolveDependencies(): DefinitionInterface;

    /**
     * @return string[]
     */
    public function getClassDependencies(): array;

    public function toPhpCode(): string;
}
