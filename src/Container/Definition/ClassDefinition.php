<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use function array_key_exists;
use function array_keys;
use function implode;
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
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = []
    ) {
        parent::__construct($className, $scope, $isEntryPoint, $isAutoloaded, $isFileBased);
        $this->constructorArguments = [];
        $this->properties = [];
        $this->needsDependencyResolution = true;
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

    public function compile(DefinitionCompilation $compilation, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);
        $tab = $this->indent(1);
        $hasProperties = empty($this->properties) === false;
        $hasConstructorArguments = empty($this->constructorArguments) === false;

        $code = "";

        if ($this->isEntryPoint() && $this->isAutoloaded() && $this->isSingleton("") && $this->getReferenceCount() === 0 && $inline === false) {
            $code .= $this->includeRelatedClasses(
                $compilation->getAutoloadConfig(),
                $compilation->getDefinitions(),
                $this->id,
                $indentationLevel
            );
            $code .= "\n";
        }

        if ($inline === false) {
            $code .= "${indent}return ";
        }

        if ($this->isSingleton("") && ($this->getReferenceCount() > 1 || $this->isEntryPoint())) {
            $code .= "\$this->singletonEntries['{$this->id}'] = ";
        }

        if ($hasProperties) {
            $code .= "\$this->setClassProperties(\n";
            $code .= "${indent}${tab}";
        }

        $code .= "new \\" . $this->getClassName() . "(";
        if ($hasConstructorArguments === false) {
            $code .= ")";
        }

        $constructorIndentationLevel = $indentationLevel + ($hasProperties ? 1 : 0);
        $constructorIndent = $this->indent($constructorIndentationLevel);
        $constructorArguments = [];

        foreach ($this->constructorArguments as $constructorArgument) {
            if (isset($constructorArgument["class"])) {
                $definition = $compilation->getDefinition($constructorArgument["class"]);

                $constructorArguments[] = "${constructorIndent}${tab}" . $this->getEntryToPhp(
                    $constructorArgument["class"],
                    $this->hash($constructorArgument["class"]),
                    $definition->isSingleton($this->id),
                    $definition,
                    $compilation,
                    $constructorIndentationLevel + 1
                );
            } elseif (array_key_exists("value", $constructorArgument)) {
                $constructorArguments[] = "${constructorIndent}${tab}" . $this->serializeValue($constructorArgument["value"]);
            }
        }

        if ($hasConstructorArguments) {
            $code .= "\n" . implode(",\n", $constructorArguments);
            $code .= "\n${constructorIndent})";
        }

        if ($hasProperties) {
            $code .= ",\n";
            $code .= "${indent}${tab}[\n";
            foreach ($this->properties as $propertyName => $property) {
                if (isset($property["class"])) {
                    $definition = $compilation->getDefinition($property["class"]);

                    $code .= "${indent}${tab}${tab}'$propertyName' => " . $this->getEntryToPhp(
                        $property["class"],
                        $this->hash($property["class"]),
                        $definition->isSingleton($this->id),
                        $definition,
                        $compilation,
                        $indentationLevel + 2
                    ) . ",\n";
                } elseif (array_key_exists("value", $property)) {
                    $code .= "${indent}${tab}${tab}'$propertyName' => " . $this->serializeValue($property["value"]) . ",\n";
                }
            }
            $code .= "${indent}${tab}]\n";
            $code .= "${indent})";
        }

        if ($inline === false) {
            $code .= ";\n";
        }

        return $code;
    }

    private function serializeValue($value): string
    {
        return var_export($value, true);
    }
}
