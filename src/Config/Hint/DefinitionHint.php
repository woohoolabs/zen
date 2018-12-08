<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Exception\ContainerException;

class DefinitionHint extends AbstractHint implements DefinitionHintInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $properties;

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
        $this->parameters = [];
        $this->properties = [];
    }

    /**
     * @param string|int|float|bool|array|null $value
     */
    public function setParameter(string $name, $value): DefinitionHint
    {
        if (is_scalar($value) === false && is_array($value) === false) {
            throw new ContainerException("Constructor argument '$name' in '$this->className' must be a scalar or an array!");
        }

        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @param string|int|float|bool|array|null $value
     */
    public function setProperty(string $name, $value): DefinitionHint
    {
        if (is_scalar($value) === false && is_array($value) === false) {
            throw new ContainerException("Property '$this->className::\$$name' must be a scalar or an array!");
        }

        $this->properties[$name] = $value;

        return $this;
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
                $id => new ClassDefinition(
                    $this->className,
                    $this->getScope(),
                    $isAutoloaded,
                    $this->parameters,
                    $this->properties
                ),
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
            $result[$this->className] = new ClassDefinition(
                $this->className,
                $this->getScope(),
                $isAutoloaded,
                $this->parameters,
                $this->properties
            );
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
