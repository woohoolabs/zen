<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use function array_key_exists;
use function array_keys;
use function implode;
use function random_int;
use function var_export;

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

    public static function singleton(
        string $className,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = []
    ): ClassDefinition {
        return new self(
            $className,
            "singleton",
            $isEntryPoint,
            $isAutoloaded,
            $isFileBased,
            $overriddenConstructorParameters,
            $overriddenProperties
        );
    }

    public static function prototype(
        string $className,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = []
    ): ClassDefinition {
        return new self(
            $className,
            "prototype",
            $isEntryPoint,
            $isAutoloaded,
            $isFileBased,
            $overriddenConstructorParameters,
            $overriddenProperties
        );
    }

    public function __construct(
        string $className,
        string $scope = "singleton",
        bool $isEntryPoint = false,
        bool $autoloaded = false,
        bool $fileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = []
    ) {
        parent::__construct($className, $scope, $isEntryPoint, $fileBased);
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
        $entry = "\$entry" . ($this->isFileBased() === false || empty($this->properties) ? "" : random_int(1, 100000));

        $code = "";
        if (empty($this->properties)) {
            if ($this->scope === "singleton" && ($this->getReferenceCount() > 1 || $this->isEntryPoint())) {
                $code .= "        return \$this->singletonEntries['{$this->id}'] = ";
            } else {
                $code .= "        return ";
            }
        } else {
            $code .= "        $entry = ";
        }

        $code .= "new \\" . $this->getClassName() . "(";

        $constructorArguments = [];
        foreach ($this->constructorArguments as $constructorArgument) {
            if (isset($constructorArgument["class"])) {
                $definition = $definitions[$constructorArgument["class"]];

                $constructorArguments[] = "            " . $this->getEntryToPhp(
                    $constructorArgument["class"],
                    $this->hash($constructorArgument["class"]),
                    $definition->getScope($this->id),
                    $definition
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
            $code .= "            $entry,\n";
            $code .= "            [\n";
            foreach ($this->properties as $propertyName => $property) {
                if (isset($property["class"])) {
                    $definition = $definitions[$property["class"]];

                    $code .= "                '$propertyName' => " . $this->getEntryToPhp(
                        $property["class"],
                        $this->hash($property["class"]),
                        $definition->getScope($this->id),
                        $definition
                    ) . ",\n";
                } elseif (array_key_exists("value", $property)) {
                    $code .= "                '$propertyName' => " . $this->serializeValue($property["value"]) . ",\n";
                }
            }
            $code .= "            ]\n";
            $code .= "        );\n";
        }

        if (empty($this->properties) === false) {
            if ($this->scope === "singleton" && ($this->getReferenceCount() > 1 || $this->isEntryPoint())) {
                $code .= "        return \$this->singletonEntries['{$this->id}'] = $entry;\n";
            } else {
                $code .= "\n        return $entry;\n";
            }
        }

        return $code;
    }

    private function serializeValue($value): string
    {
        return var_export($value, true);
    }
}
