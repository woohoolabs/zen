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
use function array_flip;
use function array_key_exists;
use function implode;

final class ContainerDependencyResolver
{
    private SimpleAnnotationReader $annotationReader;
    private PhpDocReader $typeHintReader;
    private AbstractCompilerConfig $compilerConfig;
    private bool $useConstructorInjection;
    private bool $usePropertyInjection;
    /** @var EntryPointInterface[] */
    private array $entryPoints;
    /** @var DefinitionHintInterface[] */
    private array $definitionHints;
    /** @var DefinitionInterface[] */
    private array $definitions;
    private AutoloadConfigInterface $autoloadConfig;
    /** @var string[] */
    private array $excludedAutoloadedFiles;
    /** @var string[] */
    private array $alwaysAutoloadedClases;
    private FileBasedDefinitionConfigInterface $fileBasedDefinitionConfig;
    /** @var string[] */
    private array $excludedFileBasedDefinitions;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Zen\Annotation');
        $this->typeHintReader = new PhpDocReader();

        $this->compilerConfig = $compilerConfig;
        $this->useConstructorInjection = $compilerConfig->useConstructorInjection();
        $this->usePropertyInjection = $compilerConfig->usePropertyInjection();
        $this->entryPoints = $compilerConfig->getEntryPointMap();
        $this->definitionHints = $compilerConfig->getDefinitionHints();

        $this->autoloadConfig = $compilerConfig->getAutoloadConfig();
        $this->excludedAutoloadedFiles = array_flip($this->autoloadConfig->getExcludedClasses());
        $this->alwaysAutoloadedClases = array_flip($this->autoloadConfig->getAlwaysAutoloadedClasses());

        $this->fileBasedDefinitionConfig = $compilerConfig->getFileBasedDefinitionConfig();
        $this->excludedFileBasedDefinitions = array_flip($this->fileBasedDefinitionConfig->getExcludedDefinitions());
    }

    /**
     * @return DefinitionInterface[]
     */
    public function resolveEntryPoints(): array
    {
        $this->resetDefinitions();

        foreach ($this->entryPoints as $id => $entryPoint) {
            $this->resolve($id, "", $entryPoint, false);
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

        if (array_key_exists($id, $this->entryPoints) === false) {
            throw new NotFoundException($id);
        }

        $this->resolve($id, "", $this->entryPoints[$id], true);

        return $this->definitions;
    }

    private function resolve(string $id, string $parentId, EntryPointInterface $parentEntryPoint, bool $runtime): void
    {
        if (array_key_exists($id, $this->definitions)) {
            if ($this->definitions[$id]->needsDependencyResolution()) {
                $this->resolveDependencies($id, $parentId, $parentEntryPoint, $runtime);
            }

            return;
        }

        if ($runtime) {
            $isAutoloaded = false;
            $isFileBased = false;
        } else {
            $isAutoloaded = $this->isAutoloaded($id, $parentEntryPoint);
            $isFileBased = $this->isFileBased($id, $parentEntryPoint);
        }

        if (array_key_exists($id, $this->definitionHints)) {
            $definitions = $this->definitionHints[$id]->toDefinitions(
                $this->entryPoints,
                $this->definitionHints,
                $id,
                $isAutoloaded,
                $isFileBased
            );

            foreach ($definitions as $definitionId => $definition) {
                /** @var DefinitionInterface $definition */
                if (array_key_exists($definitionId, $this->definitions) === false) {
                    $this->definitions[$definitionId] = $definition;
                    $this->resolve($definitionId, $parentId, $parentEntryPoint, $runtime);
                }
            }

            return;
        }

        $this->definitions[$id] = new ClassDefinition($id, true, array_key_exists($id, $this->entryPoints), $isAutoloaded, $isFileBased);
        $this->resolveDependencies($id, $parentId, $parentEntryPoint, $runtime);
    }

    /**
     * @throws ContainerException
     */
    private function resolveDependencies(string $id, string $parentId, EntryPointInterface $parentEntryPoint, bool $runtime): void
    {
        $this->definitions[$id]->resolveDependencies();

        if ($this->definitions[$id] instanceof ClassDefinition === false) {
            return;
        }

        if ($this->useConstructorInjection) {
            $this->resolveConstructorArguments($id, $parentId, $this->definitions[$id], $parentEntryPoint, $runtime);
        }

        if ($this->usePropertyInjection) {
            $this->resolveProperties($id, $parentId, $this->definitions[$id], $parentEntryPoint, $runtime);
        }
    }

    /**
     * @throws ContainerException
     */
    private function resolveConstructorArguments(
        string $id,
        string $parentId,
        ClassDefinition $definition,
        EntryPointInterface $parentEntryPoint,
        bool $runtime
    ): void {
        try {
            $reflectionClass = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new ContainerException("Cannot inject class: " . $id);
        }

        $constructor = $reflectionClass->getConstructor();
        if ($constructor === null) {
            return;
        }

        $paramNames = [];
        foreach ($constructor->getParameters() as $param) {
            $paramName = $param->getName();
            $paramNames[] = $paramName;

            if ($definition->isConstructorParameterOverridden($paramName)) {
                $definition->addConstructorArgumentFromOverride($paramName);
                continue;
            }

            if ($param->isOptional()) {
                $definition->addConstructorArgumentFromValue($param->getDefaultValue());
                continue;
            }

            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                throw new ContainerException(
                    "Type declaration or PHPDoc type hint for constructor parameter '$paramName' in '" .
                    "class '{$definition->getClassName()}' is missing or it is not a class!"
                );
            }

            $definition->addConstructorArgumentFromClass($paramClass);
            $this->resolve($paramClass, $id, $parentEntryPoint, $runtime);
            $this->definitions[$paramClass]->increaseReferenceCount($id, $definition->isSingleton($parentId));
        }

        $invalidConstructorParameterOverrides = array_diff($definition->getOverriddenConstructorParameters(), $paramNames);
        if ($invalidConstructorParameterOverrides !== []) {
            throw new ContainerException(
                "Class '{$definition->getClassName()}' has the following overridden constructor parameters which don't exist: " .
                implode(", ", $invalidConstructorParameterOverrides) . "!"
            );
        }
    }

    /**
     * @throws ContainerException
     */
    private function resolveProperties(
        string $id,
        string $parentId,
        ClassDefinition $definition,
        EntryPointInterface $parentEntryPoint,
        bool $runtime
    ): void {
        $class = new ReflectionClass($id);

        $propertyNames = [];
        foreach ($class->getProperties() as $property) {
            $propertyName = $property->getName();

            $propertyNames[] = $propertyName;

            if ($definition->isPropertyOverridden($propertyName)) {
                $definition->addPropertyFromOverride($propertyName);
                continue;
            }

            if ($this->annotationReader->getPropertyAnnotation($property, Inject::class) === null) {
                continue;
            }

            if ($property->isStatic()) {
                throw new ContainerException(
                    "Property '{$class->getName()}::\$$propertyName' is static and can't be injected upon!"
                );
            }

            $propertyClass = null;
            $propertyType = $property->getType();
            if ($propertyType !== null && $propertyType->isBuiltin() === false) {
                $propertyClass = $propertyType->getName();
            } else {
                $propertyClass = $this->typeHintReader->getPropertyClass($property);
            }

            if ($propertyClass === null) {
                throw new ContainerException(
                    "Type declaration or PHPDoc type hint for property $id::\$$propertyName' is missing or it is not a class!"
                );
            }

            $definition->addPropertyFromClass($propertyName, $propertyClass);
            $this->resolve($propertyClass, $id, $parentEntryPoint, $runtime);
            $this->definitions[$propertyClass]->increaseReferenceCount($id, $definition->isSingleton($parentId));
        }

        $invalidPropertyOverrides = array_diff($definition->getOverriddenProperties(), $propertyNames);
        if ($invalidPropertyOverrides !== []) {
            throw new ContainerException(
                "Class '$id' has the following overridden properties which don't exist: " .
                implode(", ", $invalidPropertyOverrides) . "!"
            );
        }
    }

    private function isAutoloaded(string $id, EntryPointInterface $parentEntryPoint): bool
    {
        if (array_key_exists($id, $this->excludedAutoloadedFiles)) {
            return false;
        }

        if (array_key_exists($id, $this->alwaysAutoloadedClases)) {
            return true;
        }

        return $parentEntryPoint->isAutoloaded($this->autoloadConfig);
    }

    private function isFileBased(string $id, EntryPointInterface $parentEntryPoint): bool
    {
        if (array_key_exists($id, $this->excludedFileBasedDefinitions)) {
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
