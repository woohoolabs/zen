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
        parent::__construct($id, "", $isEntryPoint, true, $isFileBased, 0, 0);
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
        throw new ContainerException("An autoloaded definition can not be instantiated!");
    }

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        $definition = $compilation->getDefinition($this->id);
        $id = $definition->getId();
        $hash = $definition->getHash();

        $code = $this->includeRelatedClasses(
            $compilation->getAutoloadConfig(),
            $compilation->getDefinitions(),
            $this->id,
            $indentationLevel
        );

        $code .= "\n";
        $code .= "${indent}self::\$entryPoints[\\$id::class] = '$hash';\n\n";

        if ($this->isFileBased()) {
            $code .= "${indent}return require __DIR__ . '/$hash.php';\n";
        } else {
            $code .= "${indent}return \$this->$hash();\n";
        }

        return $code;
    }
}
