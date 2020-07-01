<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

use function array_key_exists;
use function array_merge;
use function is_string;

class ContextDependentDefinitionHint implements DefinitionHintInterface
{
    private ?DefinitionHint $defaultDefinitionHint;
    /** @var DefinitionHint[] */
    private array $definitionHints = [];

    public static function create(DefinitionHint|string|null $defaultDefinitionHint = null): ContextDependentDefinitionHint
    {
        return new self($defaultDefinitionHint);
    }

    public function __construct(DefinitionHint|string|null $defaultDefinitionHint = null)
    {
        $this->defaultDefinitionHint = $this->createDefinitionHint($defaultDefinitionHint);
    }

    public function setDefaultClass(DefinitionHint|string $defaultDefinitionHint): ContextDependentDefinitionHint
    {
        $this->defaultDefinitionHint = $this->createDefinitionHint($defaultDefinitionHint);

        return $this;
    }

    /**
     * @param string[] $parentClasses
     */
    public function setClassContext(DefinitionHint|string $definitionHint, array $parentClasses): ContextDependentDefinitionHint
    {
        $definitionHint = $this->createDefinitionHint($definitionHint);

        if ($definitionHint === null) {
            return $this;
        }

        foreach ($parentClasses as $parent) {
            $this->definitionHints[$parent] = $definitionHint;
        }

        return $this;
    }

    /**
     * @internal
     *
     * @param EntryPointInterface[]     $entryPoints
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $entryPoints, array $definitionHints, string $id, bool $isAutoloaded, bool $isFileBased): array
    {
        $isEntryPoint = array_key_exists($id, $entryPoints);

        $defaultDefinition = null;
        if ($this->defaultDefinitionHint !== null) {
            $defaultDefinition = new ClassDefinition(
                $this->defaultDefinitionHint->getClassName(),
                $this->defaultDefinitionHint->isSingleton(),
                $isEntryPoint,
                $isAutoloaded,
                $isFileBased
            );
        }

        $definitions = [];
        foreach ($this->definitionHints as $parentId => $definitionHint) {
            $definitions[$parentId] = new ClassDefinition(
                $definitionHint->getClassName(),
                $definitionHint->isSingleton(),
                $isEntryPoint,
                $isAutoloaded,
                $isFileBased
            );
        }

        $result = [
            $id => new ContextDependentDefinition($id, $defaultDefinition, $definitions),
        ];

        if ($defaultDefinition !== null) {
            $result[$defaultDefinition->getClassName()] = $defaultDefinition;
        }

        $definitionHintDefinitions = [];
        foreach ($this->definitionHints as $definitionHint) {
            $definitionHintDefinitions[] = $definitionHint->toDefinitions(
                $entryPoints,
                $definitionHints,
                $definitionHint->getClassName(),
                $isAutoloaded,
                $isFileBased
            );
        }

        return array_merge($result, ...$definitionHintDefinitions);
    }

    private function createDefinitionHint(DefinitionHint|string|null $definitionHint): ?DefinitionHint
    {
        return is_string($definitionHint) ? new DefinitionHint($definitionHint) : $definitionHint;
    }
}
