<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

interface DefinitionInterface
{
    public function getId(): string;

    public function getHash(): string;

    public function needsDependencyResolution(): bool;

    public function resolveDependencies(): DefinitionInterface;

    public function toPhpCode(): string;
}
