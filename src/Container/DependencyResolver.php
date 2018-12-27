<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PhpDocReader\PhpDocReader;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use WoohooLabs\Zen\Annotation\Inject;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Exception\NotFoundException;
use function array_diff;
use function implode;
use function in_array;

class DependencyResolver
{
    /**
     * @var SimpleAnnotationReader
     */
    private $annotationReader;

    /**
     * @var PhpDocReader
     */
    private $typeHintReader;

    /**
     * @var AbstractCompilerConfig
     */
    private $compilerConfig;

    /**
     * @var EntryPointInterface[]
     */
    private $entryPoints;

    /**
     * @var DefinitionHintInterface[]
     */
    private $definitionHints;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Zen\Annotation');
        $this->typeHintReader = new PhpDocReader();
        $this->compilerConfig = $compilerConfig;
        $this->entryPoints = $compilerConfig->getEntryPointMap();
        $this->definitionHints = $compilerConfig->getDefinitionHints();
    }

    /**
     * @return DefinitionInterface[]
     */
    public function resolveEntryPoints(): array
    {
        $this->resetDefinitions();

        foreach ($this->entryPoints as $id => $entryPoint) {
            $this->resolve($id, $entryPoint, $entryPoint);
        }

        return $this->definitions;
    }

    /**
     * @return DefinitionInterface[]
     * @throws NotFoundException
     */
    public function resolveClass(string $id): array
    {
        $this->resetDefinitions();

        if (isset($this->entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        $this->resolve($id, $this->entryPoints[$id], $this->entryPoints[$id]);

        return $this->definitions;
    }

    private function resolve(string $id, ?EntryPointInterface $currentEntryPoint, EntryPointInterface $parentEntryPoint): void
    {
        if (isset($this->definitions[$id])) {
            if ($this->definitions[$id]->needsDependencyResolution()) {
                $this->resolveDependencies($id, $parentEntryPoint);
            }

            return;
        }

        $isAutoloaded = $this->isAutoloaded($currentEntryPoint);
        $isFileBased = $this->isFileBasedDefinition($parentEntryPoint);
        $isEntryPoint = $currentEntryPoint !== null;

        if (isset($this->definitionHints[$id])) {
            $definitions = $this->definitionHints[$id]->toDefinitions($this->definitionHints, $id, $isEntryPoint, $isAutoloaded, $isFileBased);
            foreach ($definitions as $definitionId => $definition) {
                /** @var DefinitionInterface $definition */
                if (isset($this->definitions[$definitionId]) === false) {
                    $this->definitions[$definitionId] = $definition;
                }
                $this->resolve($definitionId, null, $parentEntryPoint);
            }

            return;
        }

        $this->definitions[$id] = new ClassDefinition($id, "singleton", $isEntryPoint, $isAutoloaded, $isFileBased);
        $this->resolveDependencies($id, $parentEntryPoint);
    }

    private function resolveDependencies(string $id, EntryPointInterface $parentEntryPoint): void
    {
        $this->definitions[$id]->resolveDependencies();

        if ($this->compilerConfig->useConstructorInjection()) {
            $this->resolveConstructorArguments($this->definitions[$id], $parentEntryPoint);
        }

        if ($this->compilerConfig->usePropertyInjection()) {
            $this->resolveAnnotatedProperties($this->definitions[$id], $parentEntryPoint);
        }
    }

    private function resolveConstructorArguments(ClassDefinition $definition, EntryPointInterface $parentEntryPoint): void
    {
        try {
            $reflectionClass = new ReflectionClass($definition->getClassName());
        } catch (ReflectionException $e) {
            throw new ContainerException("Cannot inject class: " . $definition->getClassName());
        }

        if ($reflectionClass->getConstructor() === null) {
            return;
        }

        $paramNames = [];
        foreach ($reflectionClass->getConstructor()->getParameters() as $param) {
            $paramNames[] = $param->getName();

            if ($definition->isConstructorParameterOverridden($param->getName())) {
                $definition->addConstructorArgumentFromOverride($param->getName());
                continue;
            }

            if ($param->isOptional()) {
                $definition->addConstructorArgumentFromValue($param->getDefaultValue());
                continue;
            }

            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                throw new ContainerException(
                    "Type declaration or '@param' PHPDoc comment for constructor parameter '{$param->getName()}' in '" .
                    "class '{$definition->getClassName()}' is missing or it is not a class!"
                );
            }

            $definition->addConstructorArgumentFromClass($paramClass);
            $this->resolve($paramClass, null, $parentEntryPoint);
        }

        $invalidConstructorParameterOverrides = array_diff($definition->getOverriddenConstructorParameters(), $paramNames);
        if (empty($invalidConstructorParameterOverrides) === false) {
            throw new ContainerException(
                "Class '{$definition->getClassName()}' has the following overridden constructor parameters which don't exist: " .
                implode(", ", $invalidConstructorParameterOverrides) . "!"
            );
        }
    }

    private function resolveAnnotatedProperties(ClassDefinition $definition, EntryPointInterface $parentEntryPoint): void
    {
        $class = new ReflectionClass($definition->getClassName());

        $propertyNames = [];
        foreach ($class->getProperties() as $property) {
            $propertyNames[] = $property->getName();

            if ($definition->isPropertyOverridden($property->getName())) {
                $definition->addPropertyFromOverride($property->getName());
                continue;
            }

            /** @var Inject $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);
            if ($annotation === null) {
                continue;
            }

            if ($property->isStatic()) {
                throw new ContainerException(
                    "Property '{$class->getName()}::\${$property->getName()}' is static and can't be injected upon!"
                );
            }

            $propertyClass = $this->typeHintReader->getPropertyClass($property);
            if ($propertyClass === null) {
                throw new ContainerException(
                    "'@var' PHPDoc comment for property '" . $definition->getClassName() . "::$" . $property->getName() .
                    "' is missing or it is not a class!"
                );
            }

            $definition->addPropertyFromClass($property->getName(), $propertyClass);
            $this->resolve($propertyClass, null, $parentEntryPoint);
        }

        $invalidPropertyOverrides = array_diff($definition->getOverriddenProperties(), $propertyNames);
        if (empty($invalidPropertyOverrides) === false) {
            throw new ContainerException(
                "Class '{$definition->getClassName()}' has the following overridden properties which don't exist: " .
                implode(", ", $invalidPropertyOverrides) . "!"
            );
        }
    }

    private function isAutoloaded(?EntryPointInterface $entryPoint): bool
    {
        $autoloadConfig = $this->compilerConfig->getAutoloadConfig();

        if (in_array($entryPoint, $autoloadConfig->getExcludedClasses(), true)) {
            return false;
        }

        if ($entryPoint && ($autoloadConfig->isGlobalAutoloadEnabled() || $entryPoint->isAutoloaded())) {
            return true;
        }

        if (in_array($entryPoint, $autoloadConfig->getAlwaysAutoloadedClasses(), true)) {
            return true;
        }

        return false;
    }

    private function isFileBasedDefinition(EntryPointInterface $entryPoint): bool
    {
        $fileBasedDefinitionConfig = $this->compilerConfig->getFileBasedDefinitionConfig();

        if (in_array($entryPoint, $fileBasedDefinitionConfig->getExcludedClasses(), true)) {
            return false;
        }

        if ($entryPoint && ($fileBasedDefinitionConfig->isGlobalFileBasedDefinitionEnabled() || $entryPoint->isFileBased())) {
            return true;
        }

        if (in_array($entryPoint, $fileBasedDefinitionConfig->getAlwaysLoadedClasses(), true)) {
            return true;
        }

        return false;
    }

    private function resetDefinitions(): void
    {
        $this->definitions = [
            $this->compilerConfig->getContainerFqcn() => new SelfDefinition($this->compilerConfig->getContainerFqcn()),
            ContainerInterface::class => ReferenceDefinition::singleton(
                ContainerInterface::class,
                $this->compilerConfig->getContainerFqcn(),
                true
            ),
        ];
    }
}
