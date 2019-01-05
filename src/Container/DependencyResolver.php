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
use function implode;

final class DependencyResolver
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
     * @var bool
     */
    private $useConstructorInjection;

    /**
     * @var bool
     */
    private $usePropertyInjection;

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
     * @var string[]
     */
    private $excludedAutoloadedFiles;

    /**
     * @var string[]
     */
    private $alwaysAutoloadedFiles;

    /**
     * @var FileBasedDefinitionConfigInterface
     */
    private $fileBasedDefinitionConfig;

    /**
     * @var string[]
     */
    private $excludedFileBasedDefinitions;

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
        $this->alwaysAutoloadedFiles = array_flip($this->autoloadConfig->getAlwaysAutoloadedClasses());

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
     * @param string $id
     * @return DefinitionInterface[]
     * @throws NotFoundException
     */
    public function resolveEntryPoint($id)
    {
        $this->resetDefinitions();

        if (isset($this->entryPoints[$id]) === false) {
            throw new NotFoundException($id);
        }

        $this->resolve($id, "", $this->entryPoints[$id], true);

        return $this->definitions;
    }

    /**
     * @param string $id
     * @param string $parentId
     * @param EntryPointInterface $parentEntryPoint
     * @param bool $runtime
     * @return void
     */
    private function resolve($id, $parentId, $parentEntryPoint, $runtime)
    {
        if (isset($this->definitions[$id])) {
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
                    $this->resolve($definitionId, $parentId, $parentEntryPoint, $runtime);
                }
            }

            return;
        }

        $this->definitions[$id] = new ClassDefinition($id, true, isset($this->entryPoints[$id]), $isAutoloaded, $isFileBased);
        $this->resolveDependencies($id, $parentId, $parentEntryPoint, $runtime);
    }

    /**
     * @param string $id
     * @param string $parentId
     * @param EntryPointInterface $parentEntryPoint
     * @param bool $runtime
     * @return void
     * @throws ContainerException
     */
    private function resolveDependencies($id, $parentId, $parentEntryPoint, $runtime)
    {
        $this->definitions[$id]->resolveDependencies();

        if ($this->useConstructorInjection) {
            $this->resolveConstructorArguments($id, $parentId, $this->definitions[$id], $parentEntryPoint, $runtime);
        }

        if ($this->usePropertyInjection) {
            $this->resolveProperties($id, $parentId, $this->definitions[$id], $parentEntryPoint, $runtime);
        }
    }

    /**
     * @param string $id
     * @param string $parentId
     * @param ClassDefinition $definition
     * @param EntryPointInterface $parentEntryPoint
     * @param bool $runtime
     * @return void
     * @throws ContainerException
     */
    private function resolveConstructorArguments(
        $id,
        $parentId,
        $definition,
        $parentEntryPoint,
        $runtime
    ) {
        try {
            $reflectionClass = new ReflectionClass($definition->getClassName());
        } catch (ReflectionException $e) {
            throw new ContainerException("Cannot inject class: " . $definition->getClassName());
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
                    "Type declaration or '@param' PHPDoc comment for constructor parameter '$paramName' in '" .
                    "class '{$definition->getClassName()}' is missing or it is not a class!"
                );
            }

            $definition->addConstructorArgumentFromClass($paramClass);
            $this->resolve($paramClass, $id, $parentEntryPoint, $runtime);
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

    /**
     * @param string $id
     * @param string $parentId
     * @param ClassDefinition $definition
     * @param EntryPointInterface $parentEntryPoint
     * @param bool $runtime
     * @return void
     * @throws ContainerException
     */
    private function resolveProperties(
        $id,
        $parentId,
        $definition,
        $parentEntryPoint,
        $runtime
    ) {
        $class = new ReflectionClass($definition->getClassName());

        $propertyNames = [];
        foreach ($class->getProperties() as $property) {
            $propertyName = $property->getName();

            $propertyNames[] = $propertyName;

            if ($definition->isPropertyOverridden($propertyName)) {
                $definition->addPropertyFromOverride($propertyName);
                continue;
            }

            /** @var Inject $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);
            if ($annotation === null) {
                continue;
            }

            if ($property->isStatic()) {
                throw new ContainerException(
                    "Property '{$class->getName()}::\$$propertyName' is static and can't be injected upon!"
                );
            }

            $propertyClass = $this->typeHintReader->getPropertyClass($property);
            if ($propertyClass === null) {
                throw new ContainerException(
                    "'@var' PHPDoc comment for property '" . $definition->getClassName() . "::$" . $propertyName .
                    "' is missing or it is not a class!"
                );
            }

            $definition->addPropertyFromClass($propertyName, $propertyClass);
            $this->resolve($propertyClass, $id, $parentEntryPoint, $runtime);
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
        if (isset($this->excludedAutoloadedFiles[$id])) {
            return false;
        }

        if (isset($this->alwaysAutoloadedFiles[$id])) {
            return true;
        }

        return $parentEntryPoint->isAutoloaded($this->autoloadConfig);
    }

    private function isFileBased(string $id, EntryPointInterface $parentEntryPoint): bool
    {
        if (isset($this->excludedFileBasedDefinitions[$id])) {
            return false;
        }

        return $parentEntryPoint->isFileBased($this->fileBasedDefinitionConfig);
    }

    /**
     * @return void
     */
    private function resetDefinitions()
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
