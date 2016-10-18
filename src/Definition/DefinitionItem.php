<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

class DefinitionItem
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var array
     */
    private $constructorParams;

    /**
     * @var array
     */
    private $properties;

    public function __construct(string $className, string $scope = "singleton")
    {
        $this->className = $className;
        $this->scope = $scope;
        $this->constructorParams = [];
        $this->properties = [];
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function isSingletonScope(): bool
    {
        return $this->scope === "singleton";
    }

    public function getConstructorParams(): array
    {
        return $this->constructorParams;
    }

    public function addRequiredConstructorParam(string $className)
    {
        $this->constructorParams[] = ["class" => $className];

        return $this;
    }

    public function addOptionalConstructorParam($defaultValue)
    {
        $this->constructorParams[] = ["default" => $defaultValue];

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(string $name, string $type)
    {
        $this->properties[$name] = $type;

        return $this;
    }
}
