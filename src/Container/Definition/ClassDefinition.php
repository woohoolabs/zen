<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ClassDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $scope;

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

    public static function singleton(string $className): ClassDefinition
    {
        return new self($className);
    }

    public static function prototype(string $className): ClassDefinition
    {
        return new self($className, "prototype");
    }

    public function __construct(string $className, string $scope = "singleton", bool $autoloaded = false)
    {
        parent::__construct($className, str_replace("\\", "__", $className));
        $this->scope = $scope;
        $this->constructorArguments = [];
        $this->properties = [];
        $this->needsDependencyResolution = true;
        $this->autoloaded = $autoloaded;
    }

    public function getClassName(): string
    {
        return $this->getId();
    }

    public function addRequiredConstructorArgument(string $className): ClassDefinition
    {
        $this->constructorArguments[] = ["class" => $className, "hash" => str_replace("\\", "__", $className)];

        return $this;
    }

    public function addOptionalConstructorArgument($defaultValue): ClassDefinition
    {
        $this->constructorArguments[] = ["default" => $defaultValue];

        return $this;
    }

    public function addProperty(string $name, string $className): ClassDefinition
    {
        $this->properties[$name] = [
            "class" => $className,
            "hash" => str_replace("\\", "__", $className),
        ];

        return $this;
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

    public function isAutoloaded(): bool
    {
        return $this->autoloaded;
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

    public function toPhpCode(): string
    {
        $code = "";
        if (empty($this->properties)) {
            if ($this->scope === "singleton") {
                $code .= "        return \$this->singletonEntries['{$this->getId()}'] = ";
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
                $constructorArguments[] = "            " . $this->getEntryToPhp($constructorArgument["class"], $constructorArgument["hash"]);
            } elseif (array_key_exists("default", $constructorArgument)) {
                $constructorArguments[] = "            " . ($this->convertValueToString($constructorArgument["default"]));
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
                $code .= "                '$propertyName' => " . $this->getEntryToPhp($property["class"], $property["hash"]) . ",\n";
            }
            $code .= "            ]\n";
            $code .= "        );\n";
        }

        if (empty($this->properties) === false) {
            if ($this->scope === "singleton") {
                $code .= "        return \$this->singletonEntries['{$this->getId()}'] = \$entry;\n";
            } else {
                $code .= "\n        return \$entry;\n";
            }
        }

        return $code;
    }

    private function convertValueToString($value): string
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
                $array .= $this->convertValueToString($k) . " => " . $this->convertValueToString($v) . ",";
            }
            $array .= "]";

            return $array;
        }

        return (string) $value;
    }
}
