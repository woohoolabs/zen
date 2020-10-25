<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\Preload\PreloadInterface;

use function array_key_exists;
use function in_array;

final class PreloadDependencyResolver
{
    private PhpDocReader $typeHintReader;
    /** @var PreloadInterface[] */
    private array $preloads;
    /** @var string[] */
    private array $classes;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->typeHintReader = new PhpDocReader();

        $this->preloads = $compilerConfig->getPreloadMap();
    }

    /**
     * @return string[]
     */
    public function resolvePreloads(): array
    {
        $this->resetClasses();

        foreach ($this->preloads as $id => $preload) {
            $this->resolve($id);
        }

        return $this->classes;
    }

    /**
     * @param string $id
     */
    private function resolve($id): void
    {
        if (array_key_exists($id, $this->classes)) {
            return;
        }

        try {
            $reflectionClass = new ReflectionClass($id);

            if ($reflectionClass->isInternal()) {
                return;
            }

            if (in_array($reflectionClass->getName(), ["self", "static", "parent"], true)) {
                return;
            }

            $filename = $reflectionClass->getFileName();
            $this->classes[$id] = $filename !== false ? $filename : "";
            $this->resolveParents($reflectionClass);
            $this->resolveTraits($reflectionClass);
            $this->resolveConstructorArguments($reflectionClass);
            $this->resolveProperties($reflectionClass);
            $this->resolveMethods($reflectionClass);
        } catch (ReflectionException $exception) {
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveParents($reflectionClass): void
    {
        foreach ($reflectionClass->getInterfaceNames() as $interface) {
            $this->resolve($interface);
        }

        $parent = $reflectionClass->getParentClass();
        if ($parent === false) {
            return;
        }

        $this->resolve($parent->getName());
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveTraits($reflectionClass): void
    {
        foreach ($reflectionClass->getTraitNames() as $trait) {
            $this->resolve($trait);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveConstructorArguments($reflectionClass): void
    {
        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return;
        }

        foreach ($constructor->getParameters() as $param) {
            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                continue;
            }

            $this->resolve($paramClass);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveProperties($reflectionClass): void
    {
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyClass = null;
            $propertyType = $property->getType();

            if ($propertyType === null) {
                $propertyClass = $this->typeHintReader->getPropertyClass($property);
                if ($propertyClass !== null) {
                    $this->resolve($propertyClass);
                }
            } else if ($propertyType instanceof ReflectionNamedType && $propertyType->isBuiltin() === false) {
                $this->resolve($propertyType->getName());
            } else if ($propertyType instanceof ReflectionUnionType) {
                foreach ($propertyType->getTypes() as $type) {
                    if ($type instanceof ReflectionNamedType && $type->isBuiltin() === false) {
                        $this->resolve($type->getName());
                    }
                }
            }
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveMethods($reflectionClass): void
    {
        foreach ($reflectionClass->getMethods() as $method) {
            foreach ($method->getParameters() as $parameter) {
                $type = $parameter->getType();
                if ($type === null) {
                    $class = $this->typeHintReader->getParameterClass($parameter);
                    if ($class !== null) {
                        $this->resolve($class);
                    }
                } elseif ($type instanceof ReflectionNamedType && $type->isBuiltin() === false) {
                    $this->resolve($type->getName());
                } elseif ($type instanceof ReflectionUnionType) {
                    foreach ($type->getTypes() as $subType) {
                        if ($subType instanceof ReflectionNamedType && $subType->isBuiltin() === false) {
                            $this->resolve($subType->getName());
                        }
                    }
                }
            }

            $returnType = $method->getReturnType();
            if ($returnType instanceof ReflectionNamedType && $returnType->isBuiltin() === false) {
                $this->resolve($returnType->getName());
            } elseif ($returnType instanceof ReflectionUnionType) {
                foreach ($returnType->getTypes() as $subType) {
                    if ($subType instanceof ReflectionNamedType && $subType->isBuiltin() === false) {
                        $this->resolve($subType->getName());
                    }
                }
            }
        }
    }

    private function resetClasses(): void
    {
        $this->classes = [];
    }
}
