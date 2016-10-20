<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

class DefinitionItem
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var bool
     */
    private $isReference;

    /**
     * @var array
     */
    private $constructorParams;

    /**
     * @var array
     */
    private $properties;

    public function __construct(string $className, string $scope = "singleton", bool $isReference = false)
    {
        $this->hash = str_replace("\\", "__", $className);
        $this->className = $className;
        $this->scope = $scope;
        $this->isReference = $isReference;
        $this->constructorParams = [];
        $this->properties = [];
    }

    public function getHash(): string
    {
        return $this->hash;
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

    public function isReference(): bool
    {
        return $this->isReference;
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
