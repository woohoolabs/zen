<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

class StubDefinition implements DefinitionInterface
{
    public function getId(): string
    {
        return StubDefinition::class;
    }

    public function getHash(): string
    {
        return str_replace("\\", "__", $this->getId());
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function toPhpCode(): string
    {
        return "        // This is a dummy definition.\n";
    }
}
