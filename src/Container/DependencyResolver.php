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
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
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

    /**
     * @var AutoloadConfigInterface
     */
    private $autoloadConfig;

    /**
     * @var FileBasedDefinitionConfigInterface
     */
    private $fileBasedDefinitionConfig;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Zen\Annotation');
        $this->typeHintReader = new PhpDocReader();
        $this->compilerConfig = $compilerConfig;
        $this->entryPoints = $compilerConfig->getEntryPointMap();
        $this->definitionHints = $compilerConfig->getDefinitionHints();
        $this->autoloadConfig = $compilerConfig->getAutoloadConfig();
        $this->fileBasedDefinitionConfig = $compilerConfig->getFileBasedDefinitionConfig();
    }

    /**
     * @return DefinitionInterface[]
     */
    public function resolveEntryPoints(): array
    {
        $this->resetDefinitions();

        foreach ($this->entryPoints as $id => $entryPoint) {
            $this->resolve($id, "", $entryPoint);
        }

        return $this->definitions;
    }

    /**
     * @return DefinitionInterface[]
     * @throws NotFoundException
     */
    public function resolveEntryPoint(string $id): array
    {
        $this->resetDefinitions();

        if (isset($this->entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        $this->resolve($id, "", $this->entryPoints[$id]);

        return $this->definitions;
    }

    private function resolve(string $id, string $parentId, EntryPointInterface $parentEntryPoint): void
    {
        if (isset($this->definitions[$id])) {
            if ($this->definitions[$id]->needsDependencyResolution()) {
                $this->resolveDependencies($id, $parentId, $parentEntryPoint);
            }

            return;
        }

        $isAutoloaded = $this->isAutoloaded($id, $parentEntryPoint);
        $isFileBased = $this->isFileBased($id, $parentEntryPoint);

        if (isset($this->definitionHints[$id])) {
            $definitions = $this->definitionHints[$id]->toDefinitions(
                $this->entryPoints,
                $this->definitionHints,
                $id,
                $isAutoloaded,
                $isFileBased
            );

            foreach ($definitions as $definitionId => $definition) {
                /** @var DefinitionInterface $definition */
                if (isset($this->definitions[$definitionId]) === false) {
                    $this->definitions[$definitionId] = $definition;
                    $this->resolve($definitionId, $parentId, $parentEntryPoint);
                }
            }

            return;
        } else {
            $this->definitions[$id] = new ClassDefinition($id, "singleton", isset($this->entryPoints[$id]), $isAutoloaded, $isFileBased);
            $this->resolveDependencies($id, $parentId, $parentEntryPoint);
        }
    }

    private function resolveDependencies(string $id, string $parentId, EntryPointInterface $parentEntryPoint): void
    {
        $this->definitions[$id]->resolveDependencies();

        if ($this->compilerConfig->useConstructorInjection()) {
            $this->resolveConstructorArguments($id, $parentId, $this->definitions[$id], $parentEntryPoint);
        }

        if ($this->compilerConfig->usePropertyInjection()) {
            $this->resolveAnnotatedProperties($id, $parentId, $this->definitions[$id], $parentEntryPoint);
        }
    }

    private function resolveConstructorArguments(
        string $id,
        string $parentId,
        ClassDefinition $definition,
        EntryPointInterface $parentEntryPoint
    ): void {
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
            $this->resolve($paramClass, $id, $parentEntryPoint);
            $this->definitions[$paramClass]->increaseReferenceCount($id, $definition->isSingleton($parentId));
        }

        $invalidConstructorParameterOverrides = array_diff($definition->getOverriddenConstructorParameters(), $paramNames);
        if (empty($invalidConstructorParameterOverrides) === false) {
            throw new ContainerException(
                "Class '{$definition->getClassName()}' has the following overridden constructor parameters which don't exist: " .
                implode(", ", $invalidConstructorParameterOverrides) . "!"
            );
        }
    }

    private function resolveAnnotatedProperties(string $id, string $parentId, ClassDefinition $definition, EntryPointInterface $parentEntryPoint): void
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
            $this->resolve($propertyClass, $id, $parentEntryPoint);
            $this->definitions[$propertyClass]->increaseReferenceCount($id, $definition->isSingleton($parentId));
        }

        $invalidPropertyOverrides = array_diff($definition->getOverriddenProperties(), $propertyNames);
        if (empty($invalidPropertyOverrides) === false) {
            throw new ContainerException(
                "Class '{$definition->getClassName()}' has the following overridden properties which don't exist: " .
                implode(", ", $invalidPropertyOverrides) . "!"
            );
        }
    }

    private function isAutoloaded(string $id, EntryPointInterface $parentEntryPoint): bool
    {
        if (in_array($id, $this->autoloadConfig->getExcludedClasses(), true)) {
            return false;
        }

        if (in_array($id, $this->autoloadConfig->getAlwaysAutoloadedClasses(), true)) {
            return true;
        }

        return $parentEntryPoint->isAutoloaded($this->autoloadConfig);
    }

    private function isFileBased(string $id, EntryPointInterface $parentEntryPoint): bool
    {
        if (in_array($id, $this->fileBasedDefinitionConfig->getExcludedDefinitions(), true)) {
            return false;
        }

        return $parentEntryPoint->isFileBased($this->fileBasedDefinitionConfig);
    }

    private function resetDefinitions(): void
    {
        $this->definitions = [
            ContainerInterface::class => ReferenceDefinition::singleton(
                ContainerInterface::class,
                $this->compilerConfig->getContainerFqcn(),
                true
            ),
            $this->compilerConfig->getContainerFqcn() => new SelfDefinition($this->compilerConfig->getContainerFqcn()),
        ];
    }
}
