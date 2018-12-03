<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ClassDefinition extends AbstractDefinition
{
    /**
     * @var array
     */
    private $constructorArguments;

    /**
     * @var array
     */
    private $properties;

    /**
     * @var bool
     */
    private $needsDependencyResolution;

    /**
     * @var bool
     */
    private $autoloaded;

    /**
     * @var array
     */
    private $overriddenConstructorParameters;

    /**
     * @var array
     */
    private $overriddenProperties;

    public static function singleton(string $className): ClassDefinition
    {
        return new self($className);
    }

    public static function prototype(string $className): ClassDefinition
    {
        return new self($className, "prototype");
    }

    public function __construct(
        string $className,
        string $scope = "singleton",
        bool $autoloaded = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = []
    ) {
        parent::__construct($className, $scope);
        $this->constructorArguments = [];
        $this->properties = [];
        $this->needsDependencyResolution = true;
        $this->autoloaded = $autoloaded;
        $this->overriddenConstructorParameters = $overriddenConstructorParameters;
        $this->overriddenProperties = $overriddenProperties;
    }

    public function getClassName(): string
    {
        return $this->id;
    }

    public function addConstructorArgumentFromClass(string $className): ClassDefinition
    {
        $this->constructorArguments[] = ["class" => $className];

        return $this;
    }

    public function addConstructorArgumentFromValue($value): ClassDefinition
    {
        $this->constructorArguments[] = ["value" => $value];

        return $this;
    }

    public function addConstructorArgumentFromOverride(string $name): ClassDefinition
    {
        $this->constructorArguments[] = ["value" => $this->overriddenConstructorParameters[$name] ?? null];

        return $this;
    }

    public function addPropertyFromClass(string $name, string $className): ClassDefinition
    {
        $this->properties[$name] = ["class" => $className];

        return $this;
    }

    public function addPropertyFromOverride(string $name): ClassDefinition
    {
        $this->properties[$name] = ["value" => $this->overriddenProperties[$name] ?? null];

        return $this;
    }

    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
    }

    public function needsDependencyResolution(): bool
    {
        return $this->needsDependencyResolution;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        $this->needsDependencyResolution = false;

        return $this;
    }

    public function isConstructorParameterOverridden(string $name): bool
    {
        return isset($this->overriddenConstructorParameters[$name]);
    }

    public function getOverriddenConstructorParameters(): array
    {
        return array_keys($this->overriddenConstructorParameters);
    }

    public function isPropertyOverridden(string $name): bool
    {
        return array_key_exists($name, $this->overriddenProperties);
    }

    public function getOverriddenProperties(): array
    {
        return array_keys($this->overriddenProperties);
    }

    /**
     * @return string[]
     */
    public function getClassDependencies(): array
    {
        $dependencies = [];

        foreach ($this->constructorArguments as $constructorArgument) {
            if (isset($constructorArgument["class"])) {
                $dependencies[] = $constructorArgument["class"];
            }
        }

        foreach ($this->properties as $property) {
            if (isset($property["class"])) {
                $dependencies[] = $property["class"];
            }
        }

        return $dependencies;
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        $code = "";
        if (empty($this->properties)) {
            if ($this->scope === "singleton") {
                $code .= "        return \$this->singletonEntries['{$this->id}'] = ";
            } else {
                $code .= "        return ";
            }
        } else {
            $code .= "        \$entry = ";
        }

        $code .= "new \\" . $this->getClassName() . "(";

        $constructorArguments = [];
        foreach ($this->constructorArguments as $constructorArgument) {
            if (isset($constructorArgument["class"])) {
                $definition = $definitions[$constructorArgument["class"]];

                $constructorArguments[] = "            " . $this->getEntryToPhp(
                    $definition->getId($this->id),
                    $definition->getHash($this->id),
                    $definition->getScope($this->id)
                );
            } elseif (array_key_exists("value", $constructorArgument)) {
                $constructorArguments[] = "            " . $this->serializeValue($constructorArgument["value"]);
            }
        }
        if (empty($constructorArguments)) {
            $code .= ");\n";
        } else {
            $code .= "\n";
            $code .= implode(",\n", $constructorArguments);
            $code .= "\n        );\n";
        }

        if (empty($this->properties) === false) {
            $code .= "        \$this->setProperties(\n";
            $code .= "            \$entry,\n";
            $code .= "            [\n";
            foreach ($this->properties as $propertyName => $property) {
                if (isset($property["class"])) {
                    $definition = $definitions[$property["class"]];

                    $code .= "                '$propertyName' => " . $this->getEntryToPhp(
                            $definition->getId($this->id),
                            $definition->getHash($this->id),
                            $definition->getScope($this->id)
                        ) . ",\n";
                } elseif (array_key_exists("value", $property)) {
                    $code .= "                '$propertyName' => " . $this->serializeValue($property["value"]) . ",\n";
                }
            }
            $code .= "            ]\n";
            $code .= "        );\n";
        }

        if (empty($this->properties) === false) {
            if ($this->scope === "singleton") {
                $code .= "        return \$this->singletonEntries['{$this->id}'] = \$entry;\n";
            } else {
                $code .= "\n        return \$entry;\n";
            }
        }

        return $code;
    }

    private function serializeValue($value): string
    {
        if (\is_string($value)) {
            return '"' . $value . '"';
        }

        if ($value === null) {
            return "null";
        }

        if (\is_bool($value)) {
            return $value ? "true" : "false";
        }

        if (\is_array($value)) {
            $array = "[";
            foreach ($value as $k => $v) {
                $array .= $this->serializeValue($k) . " => " . $this->serializeValue($v) . ",";
            }
            $array .= "]";

            return $array;
        }

        return (string) $value;
    }
}
