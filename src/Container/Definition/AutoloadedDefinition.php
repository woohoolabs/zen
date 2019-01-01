<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;

final class AutoloadedDefinition extends AbstractDefinition
{
    public function __construct(string $id, bool $isEntryPoint = false, bool $isFileBased = false)
    {
        $this->id = $id;
        parent::__construct($id, "", $isEntryPoint, true, $isFileBased, 0);
    }

    public function isSingleton(string $parentId): bool
    {
        return false;
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

    public function compile(DefinitionCompilation $definitionCompilation, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        $definition = $definitionCompilation->getDefinition($this->id);
        $id = $definition->getId("");
        $hash = $definition->getHash("");

        $code = $this->includeRelatedClasses(
            $definitionCompilation->getAutoloadConfig(),
            $definitionCompilation->getDefinitions(),
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
