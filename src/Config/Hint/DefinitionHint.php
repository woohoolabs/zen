<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Exception\ContainerException;
use function array_merge;
use function is_array;
use function is_scalar;

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
     * @param EntryPointInterface[] $entryPoints
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     * @internal
     */
    public function toDefinitions(array $entryPoints, array $definitionHints, string $id, bool $isAutoloaded, bool $isFileBased): array
    {
        $isEntryPoint = isset($entryPoints[$id]);

        if ($this->className === $id) {
            return [
                $id => new ClassDefinition(
                    $this->className,
                    $this->isSingleton(),
                    $isEntryPoint,
                    $isAutoloaded,
                    $isFileBased,
                    $this->parameters,
                    $this->properties
                ),
            ];
        }

        $result = [
            $id => new ReferenceDefinition($id, $this->className, $this->isSingleton(), $isEntryPoint, $isAutoloaded, $isFileBased),
        ];

        if (isset($definitionHints[$this->className])) {
            $definitions = $definitionHints[$this->className]->toDefinitions(
                $entryPoints,
                $definitionHints,
                $this->className,
                false,
                $isFileBased
            );

            foreach ($definitions as $definition) {
                $definition->increaseReferenceCount($id, $this->singleton);
            }

            return array_merge($result, $definitions);
        }

        $classDefinition = new ClassDefinition(
            $this->className,
            $this->singleton,
            isset($entryPoints[$this->className]),
            false,
            $isFileBased,
            $this->parameters,
            $this->properties
        );
        $result[$this->className] = $classDefinition->increaseReferenceCount($id, $this->singleton);

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
