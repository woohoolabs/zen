<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

class SelfDefinition extends AbstractDefinition
{
    public function __construct(string $className)
    {
        parent::__construct($className, "", false, false, false, 0, 0);
    }

    public function increaseReferenceCount(string $parentId, bool $isParentSingleton): DefinitionInterface
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

    /**
     * @return mixed
     */
    public function instantiate(DefinitionInstantiation $instantiation, string $parentId)
    {
        return $instantiation->getContainer();
    }

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        if ($inline) {
            return "\$this";
        }

        return "${indent}return \$this;\n";
    }
}
