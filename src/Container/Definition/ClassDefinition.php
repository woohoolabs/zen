<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use Closure;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

use function array_key_exists;
use function array_keys;
use function var_export;

class ClassDefinition extends AbstractDefinition
{
    /** @var array<int, array{class?: string, value?: mixed}> */
    private array $constructorArguments;
    /** @var array<string, array{class?: string, value?: mixed}> */
    private array $properties;
    private bool $needsDependencyResolution;
    /** @var array<string, string|int|float|bool|array<mixed, mixed>|null> */
    private array $overriddenConstructorParameters;
    /** @var array<string, string|int|float|bool|array<mixed, mixed>|null> */
    private array $overriddenProperties;

    /**
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenConstructorParameters
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenProperties
     */
    public static function singleton(
        string $className,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = [],
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ClassDefinition {
        return new self(
            $className,
            true,
            $isEntryPoint,
            $isFileBased,
            $overriddenConstructorParameters,
            $overriddenProperties,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
    }

    /**
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenConstructorParameters
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenProperties
     */
    public static function prototype(
        string $className,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = [],
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ClassDefinition {
        return new self(
            $className,
            false,
            $isEntryPoint,
            $isFileBased,
            $overriddenConstructorParameters,
            $overriddenProperties,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
    }

    /**
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenConstructorParameters
     * @param array<string, string|int|float|bool|array<mixed, mixed>|null> $overriddenProperties
     */
    public function __construct(
        string $className,
        bool $isSingleton = true,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        array $overriddenConstructorParameters = [],
        array $overriddenProperties = [],
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ) {
        parent::__construct(
            $className,
            $isSingleton,
            $isEntryPoint,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
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

    public function addConstructorArgumentFromValue(mixed $value): ClassDefinition
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

    public function isDefinitionInlinable(string $parentId = ""): bool
    {
        if ($this->isFileBased($parentId)) {
            return true;
        }

        if ($this->getSingletonReferenceCount() >= 1 || $this->getPrototypeReferenceCount() >= 1) {
            return false;
        }

        return true;
    }

    public function isConstructorParameterOverridden(string $name): bool
    {
        return array_key_exists($name, $this->overriddenConstructorParameters);
    }

    /**
     * @return array<int, string|int|float|bool|array<mixed, mixed>|null>
     */
    public function getOverriddenConstructorParameters(): array
    {
        return array_keys($this->overriddenConstructorParameters);
    }

    public function isPropertyOverridden(string $name): bool
    {
        return array_key_exists($name, $this->overriddenProperties);
    }

    /**
     * @return array<int, string|int|float|bool|array<mixed, mixed>|null>
     */
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
            if (array_key_exists("class", $constructorArgument)) {
                $dependencies[] = $constructorArgument["class"];
            }
        }

        foreach ($this->properties as $property) {
            if (array_key_exists("class", $property)) {
                $dependencies[] = $property["class"];
            }
        }

        return $dependencies;
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     */
    public function instantiate($instantiation, $parentId): mixed
    {
        if ($this->singleton === false) {
            return $this->instantiateClass($instantiation);
        }

        return $instantiation->singletonEntries[$this->id] ?? $instantiation->singletonEntries[$this->id] = $this->instantiateClass($instantiation);
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string {
        $indent = $this->indent($indentationLevel);
        $tab = $this->indent(1);
        $hasProperties = $this->properties !== [];
        $hasConstructorArguments = $this->constructorArguments !== [];

        $code = "";

        if ($inline === false) {
            $code .= "${indent}return ";
        }

        if ($this->isSingletonCheckEliminable($parentId) === false) {
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

        foreach ($this->constructorArguments as $constructorArgument) {
            if (array_key_exists("class", $constructorArgument)) {
                $definition = $compilation->getDefinition($constructorArgument["class"]);

                $code .= "\n${constructorIndent}${tab}" . $this->compileEntryReference(
                    $definition,
                    $compilation,
                    $constructorIndentationLevel + 1,
                    $preloadedClasses
                ) . ",";
            } elseif (array_key_exists("value", $constructorArgument)) {
                $code .= "\n${constructorIndent}${tab}" . $this->serializeValue($constructorArgument["value"]) . ",";
            }
        }

        if ($hasConstructorArguments) {
            $code .= "\n${constructorIndent})";
        }

        if ($hasProperties) {
            $code .= ",\n";
            $code .= "${indent}${tab}[\n";
            foreach ($this->properties as $propertyName => $property) {
                if (array_key_exists("class", $property)) {
                    $definition = $compilation->getDefinition($property["class"]);

                    $code .= "${indent}${tab}${tab}'$propertyName' => " . $this->compileEntryReference(
                        $definition,
                        $compilation,
                        $indentationLevel + 2,
                        $preloadedClasses
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

    private function instantiateClass(DefinitionInstantiation $instantiation): mixed
    {
        $arguments = [];
        foreach ($this->constructorArguments as $argument) {
            if (array_key_exists("class", $argument)) {
                $arguments[] = $instantiation->definitions[$argument["class"]]->instantiate($instantiation, $this->id);
            } elseif (array_key_exists("value", $argument)) {
                $arguments[] = $argument["value"];
            }
        }

        $className = $this->id;
        $object = new $className(...$arguments);

        if ($this->properties !== []) {
            $properties = $this->properties;
            Closure::bind(
                static function () use ($instantiation, $className, $object, $properties): void {
                    foreach ($properties as $name => $property) {
                        if (array_key_exists("class", $property)) {
                            $object->$name = $instantiation->definitions[$property["class"]]->instantiate($instantiation, $className);
                        } elseif (array_key_exists("value", $property)) {
                            $object->$name = $property["value"];
                        }
                    }
                },
                null,
                $object
            )->__invoke();
        }

        return $object;
    }

    private function serializeValue(mixed $value): string
    {
        return var_export($value, true);
    }
}
