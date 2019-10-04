<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Exception\ContainerException;

final class AutoloadedDefinition extends AbstractDefinition
{
    public function __construct(string $id, bool $isEntryPoint = false, bool $isFileBased = false)
    {
        $this->id = $id;
        parent::__construct($id, true, $isEntryPoint, true, $isFileBased, 0, 0);
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassDependencies(): array
    {
        return [];
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     * @return mixed
     */
    public function instantiate($instantiation, $parentId)
    {
        throw new ContainerException("An autoloaded definition can not be instantiated!");
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string {
        $indent = $this->indent($indentationLevel);

        $definition = $compilation->getDefinition($this->id);
        $id = $definition->getId();
        $hash = $definition->getHash();

        $code = $this->includeRelatedClasses(
            $compilation->getAutoloadConfig(),
            $compilation->getDefinitions(),
            $this->id,
            $indentationLevel,
            $preloadedClasses
        );

        $code .= "\n";
        $code .= "${indent}self::\$entryPoints['$id'] = '$hash';\n\n";

        if ($this->isFileBased()) {
            $code .= "${indent}return require __DIR__ . '/$hash.php';\n";
        } else {
            $code .= "${indent}return \$this->$hash();\n";
        }

        return $code;
    }
}
