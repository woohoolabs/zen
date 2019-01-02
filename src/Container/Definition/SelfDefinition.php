<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

class SelfDefinition extends AbstractDefinition
{
    public function __construct(string $className)
    {
        parent::__construct($className, "", false, false, false, 0);
    }

    public function increaseReferenceCount(string $parentId = ""): DefinitionInterface
    {
        return $this;
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

    public function compile(DefinitionCompilation $compilation, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        if ($inline) {
            return "\$this";
        }

        return "${indent}return \$this;\n";
    }
}
