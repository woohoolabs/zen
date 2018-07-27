<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

class ContextDependentDefinitionHint implements DefinitionHintInterface
{
    /**
     * @var DefinitionHint|null
     */
    private $defaultDefinitionHint;

    /**
     * @var DefinitionHint[]
     */
    private $definitionHints = [];

    /**
     * @param DefinitionHint|string|null $defaultDefinitionHint
     */
    public static function create($defaultDefinitionHint = null): ContextDependentDefinitionHint
    {
        return new self($defaultDefinitionHint);
    }

    /**
     * @param DefinitionHint|string|null $defaultDefinitionHint
     */
    public function __construct($defaultDefinitionHint = null)
    {
        $this->defaultDefinitionHint = $this->createDefinitionHint($defaultDefinitionHint);
    }

    /**
     * @param DefinitionHint|string $defaultDefinitionHint
     */
    public function setDefaultClass($defaultDefinitionHint): ContextDependentDefinitionHint
    {
        $this->defaultDefinitionHint = $this->createDefinitionHint($defaultDefinitionHint);

        return $this;
    }

    /**
     * @param string[] $parentClasses
     * @param DefinitionHint|string $definitionHint
     */
    public function setClassContext($definitionHint, array $parentClasses): ContextDependentDefinitionHint
    {
        $definitionHint = $this->createDefinitionHint($definitionHint);

        foreach ($parentClasses as $parent) {
            $this->definitionHints[$parent] = $definitionHint;
        }

        return $this;
    }

    /**
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $definitionHints, string $id, bool $isAutoloaded): array
    {
        $defaultDefinition = null;
        if ($this->defaultDefinitionHint) {
            $defaultDefinition = new ClassDefinition($this->defaultDefinitionHint->getClassName(), $this->defaultDefinitionHint->getScope());
        }

        $definitions = [];
        foreach ($this->definitionHints as $parentId => $definitionHint) {
            $definitions[$parentId] = new ClassDefinition($definitionHint->getClassName(), $definitionHint->getScope());
        }

        $result = [
            $id => new ContextDependentDefinition($id, $defaultDefinition, $definitions),
        ];

        if ($this->defaultDefinitionHint) {
            $result[$defaultDefinition->getClassName()] = $defaultDefinition;
        }

        foreach ($this->definitionHints as $definitionHint) {
            $result = array_merge(
                $result,
                $definitionHint->toDefinitions($definitionHints, $definitionHint->getClassName(), $isAutoloaded)
            );
        }

        return $result;
    }

    /**
     * @param DefinitionHint|string|null $definitionHint
     */
    private function createDefinitionHint($definitionHint): ?DefinitionHint
    {
        return \is_string($definitionHint) ? new DefinitionHint($definitionHint) : $definitionHint;
    }
}
