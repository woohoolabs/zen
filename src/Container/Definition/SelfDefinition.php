<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

class SelfDefinition extends AbstractDefinition
{
    public function __construct(string $className)
    {
        parent::__construct($className, "", true, false, false);
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function getClassDependencies(): array
    {
        return [];
    }

    public function compile(DefinitionCompilation $compilation): string
    {
        return "        return \$this;\n";
    }
}
