<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

class ContextDependentDefinitionHint implements DefinitionHintInterface
{
    /**
     * @var DefinitionHint[]
     */
    private $definitionHints;

    public static function create(): ContextDependentDefinitionHint
    {
        return new self();
    }

    public function __construct()
    {
    }

    /**
     * @param string[] $parentClasses
     * @param DefinitionHint|string $definitionHint
     */
    public function setClassContext(array $parentClasses, $definitionHint)
    {
        $definitionHint = is_string($definitionHint) ? new DefinitionHint($definitionHint) : $definitionHint;

        foreach ($parentClasses as $parent) {
            $this->definitionHints[$parent] = $definitionHint;
        }
    }

    /**
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $definitionHints, string $id, bool $isAutoloaded): array
    {
        $definitions = [];
        foreach ($this->definitionHints as $definitionHint) {
            $definitions[] = new ClassDefinition($definitionHint->getClassName(), $definitionHint->getScope());
        }

        $result = [
            $id => new ContextDependentDefinition($id, $this->definitionHints),
        ];

        foreach ($this->definitionHints as $definitionHint) {
            $result = array_merge(
                $result,
                $definitionHint->toDefinitions($definitionHints, $definitionHint->getClassName(), $isAutoloaded)
            );
        }

        return $result;
    }
}
