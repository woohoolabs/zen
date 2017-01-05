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

    private $needsDependencyResolution;

    public static function singleton(string $className): ClassDefinition
    {
        return new self($className);
    }

    public static function prototype(string $className): ClassDefinition
    {
        return new self($className, "prototype");
    }

    public function __construct(string $className, string $scope = "singleton")
    {
        parent::__construct($className, str_replace("\\", "__", $className));
        $this->scope = $scope;
        $this->constructorArguments = [];
        $this->properties = [];
        $this->needsDependencyResolution = true;
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
        $this->properties[$name] = str_replace("\\", "__", $className);

        return $this;
    }

    public function needsDependencyResolution(): bool
    {
        return $this->needsDependencyResolution;
    }

    public function resolveDependencies()
    {
        $this->needsDependencyResolution = false;

        return $this;
    }

    public function toPhpCode(): string
    {
        $code = "        \$entry = new \\" . $this->getClassName() . "(";

        $constructorArguments = [];
        foreach ($this->constructorArguments as $constructorArgument) {
            if (isset($constructorArgument["class"])) {
                $constructorArguments[] = "            " . $this->getEntryToPhp($constructorArgument["hash"]);
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
            foreach ($this->properties as $propertyName => $propertyHash) {
                $code .= "                '$propertyName' => '$propertyHash',\n";
            }
            $code .= "            ]\n";
            $code .= "        );\n";
        }

        $code .= "\n";

        if ($this->scope === "singleton") {
            $code .= "        return \$this->singletonEntries['" . $this->getHash() . "'] = \$entry;\n";
        } else {
            $code .= "        return \$entry;\n";
        }

        return $code;
    }

    private function convertValueToString($value): string
    {
        if (is_string($value)) {
            return '"' . $value . '"';
        }

        if ($value === null) {
            return "null";
        }

        if (is_bool($value)) {
            return $value === true ? "true" : "false";
        }

        if (is_array($value)) {
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
