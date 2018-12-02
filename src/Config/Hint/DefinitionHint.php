<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;

class DefinitionHint extends AbstractHint implements DefinitionHintInterface
{
    /**
     * @var string
     */
    private $className;

    public static function singleton(string $className): DefinitionHint
    {
        return new self($className, "singleton");
    }

    public static function prototype(string $className): DefinitionHint
    {
        return new self($className, "prototype");
    }

    public function __construct(string $className, string $scope = "singleton")
    {
        parent::__construct($scope);
        $this->className = $className;
    }

    /**
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     * @internal
     */
    public function toDefinitions(array $definitionHints, string $id, bool $isAutoloaded): array
    {
        if ($this->className === $id) {
            return [
                $id => new ClassDefinition($this->className, $this->getScope(), $isAutoloaded),
            ];
        }

        $result = [
            $id => new ReferenceDefinition($id, $this->className, $this->getScope()),
        ];

        if (isset($definitionHints[$this->className])) {
            $result = array_merge(
                $result,
                $definitionHints[$this->className]->toDefinitions($definitionHints, $this->className, $isAutoloaded)
            );
        } else {
            $result[$this->className] = new ClassDefinition($this->className, $this->getScope(), $isAutoloaded);
        }

        return $result;
    }

    /**
     * @internal
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}
