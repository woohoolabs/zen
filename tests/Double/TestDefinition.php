<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\AbstractDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;

class TestDefinition extends AbstractDefinition
{
    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function isAutoloaded(): bool
    {
        return false;
    }

    public function getClassDependencies(): array
    {
        return [];
    }

    public function compile(DefinitionCompilation $compilation): string
    {
        return "";
    }
}
