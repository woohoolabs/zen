<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionException;
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
     * @return void
     */
    private function resolve($id)
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
     * @return void
     */
    private function resolveParents($reflectionClass)
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
     * @return void
     */
    private function resolveTraits($reflectionClass)
    {
        foreach ($reflectionClass->getTraitNames() as $trait) {
            $this->resolve($trait);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function resolveConstructorArguments($reflectionClass)
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
     * @return void
     */
    private function resolveProperties($reflectionClass)
    {
        foreach ($reflectionClass->getProperties() as $property) {
            $propertyClass = null;
            $propertyType = $property->getType();

            if ($propertyType !== null) {
                $propertyClass = $propertyType->getName();
                if ($propertyType->isBuiltin()) {
                    continue;
                }
            } else {
                $propertyClass = $this->typeHintReader->getPropertyClass($property);
                if ($propertyClass === null) {
                    continue;
                }
            }

            $this->resolve($propertyClass);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return void
     */
    private function resolveMethods($reflectionClass)
    {
        foreach ($reflectionClass->getMethods() as $method) {
            foreach ($method->getParameters() as $parameter) {
                $parameterClass = $this->typeHintReader->getParameterClass($parameter);
                if ($parameterClass === null) {
                    continue;
                }

                $this->resolve($parameterClass);
            }

            $returnType = $method->getReturnType();
            if ($returnType !== null && $returnType->isBuiltin() === false) {
                $this->resolve($returnType->getName());
            }
        }
    }

    private function resetClasses(): void
    {
        $this->classes = [];
    }
}
