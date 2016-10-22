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
    private $constructorParams;

    /**
     * @var array
     */
    private $properties;

    public function __construct(string $className, string $scope = "singleton")
    {
        parent::__construct($className, str_replace("\\", "__", $className));
        $this->scope = $scope;
        $this->constructorParams = [];
        $this->properties = [];
    }

    public function addRequiredConstructorParam(string $className)
    {
        $this->constructorParams[] = ["class" => $className, "hash" => str_replace("\\", "__", $className)];

        return $this;
    }

    public function getClassName(): string
    {
        return $this->getId();
    }

    public function addOptionalConstructorParam($defaultValue)
    {
        $this->constructorParams[] = ["default" => $defaultValue];

        return $this;
    }

    public function addProperty(string $name, string $className)
    {
        $this->properties[$name] = str_replace("\\", "__", $className);

        return $this;
    }

    public function toPhpCode(): string
    {
        $code = "        \$entry = new \\" . $this->getClassName() . "(";

        $constructorParams = [];
        foreach ($this->constructorParams as $constructorParam) {
            if (isset($constructorParam["class"])) {
                $constructorParams[] = "            \$this->getEntry('" . $constructorParam["hash"] . "')";
            } elseif (isset($constructorParam["default"])) {
                $constructorParams[] = "            " . ($this->convertValueToString($constructorParam["default"]));
            }
        }
        if (empty($constructorParams)) {
            $code .= ");\n";
        } else {
            $code .= "\n";
            $code .= implode(",\n", $constructorParams);
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

        if ($this->scope === "singleton") {
            $code .= "\n        \$this->singletonEntries['" . $this->getHash() . "'] = \$entry;\n\n";
        }

        $code .= "        return \$entry;\n";

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

        return $value;
    }
}
