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
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Exception\ContainerException;

class DependencyResolver
{
    /**
     * @var AbstractCompilerConfig
     */
    private $compilerConfig;

    /**
     * @var DefinitionHintInterface[]
     */
    private $definitionHints;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    /**
     * @var SimpleAnnotationReader
     */
    private $annotationReader;

    /**
     * @var PhpDocReader
     */
    private $typeHintReader;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $definitionHints = [];
        foreach ($compilerConfig->getContainerConfigs() as $containerConfig) {
            $definitionHints = array_merge($definitionHints, $containerConfig->createDefinitionHints());
        }

        $this->compilerConfig = $compilerConfig;
        $this->definitionHints = $definitionHints;
        $this->setAnnotationReader();
        $this->typeHintReader = new PhpDocReader();

        $this->definitions = [
            $this->compilerConfig->getContainerFqcn() => new SelfDefinition($this->compilerConfig->getContainerFqcn()),
            ContainerInterface::class => new ReferenceDefinition(
                ContainerInterface::class,
                $this->compilerConfig->getContainerFqcn())
        ];
    }

    public function resolveEntryPoints(): void
    {
        foreach ($this->compilerConfig->getContainerConfigs() as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    $this->resolve($id);
                }
            }
        }
    }

    private function resolve(string $id): void
    {
        if (isset($this->definitions[$id])) {
            if ($this->definitions[$id]->needsDependencyResolution()) {
                $this->resolveDependencies($id);
            }
            return;
        }

        if (isset($this->definitionHints[$id])) {
            $definitions = $this->definitionHints[$id]->toDefinitions($this->definitionHints, $id);
            foreach ($definitions as $definitionId => $definition) {
                /** @var DefinitionInterface $definition */
                if (isset($this->definitions[$definitionId]) === false) {
                    $this->definitions[$definitionId] = $definition;
                }
                $this->resolve($definitionId);
            }

            return;
        }

        $this->definitions[$id] = new ClassDefinition($id);
        $this->resolveDependencies($id);
    }

    private function resolveDependencies(string $id): void
    {
        $this->definitions[$id]->resolveDependencies();

        if ($this->compilerConfig->useConstructorInjection()) {
            $this->resolveConstructorArguments($this->definitions[$id]);
        }

        if ($this->compilerConfig->usePropertyInjection()) {
            $this->resolveAnnotatedProperties($this->definitions[$id]);
        }
    }

    /**
     * @return DefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    private function resolveConstructorArguments(ClassDefinition $definition): void
    {
        try {
            $reflectionClass = new ReflectionClass($definition->getClassName());
        } catch (ReflectionException $e) {
            throw new ContainerException("Cannot inject class: " . $definition->getClassName());
        }

        if ($reflectionClass->getConstructor() === null) {
            return;
        }

        foreach ($reflectionClass->getConstructor()->getParameters() as $param) {
            if ($param->isOptional()) {
                $definition->addOptionalConstructorArgument($param->getDefaultValue());
                continue;
            }

            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                throw new ContainerException(
                    "Type declaration or '@param' PHPDoc comment for constructor parameter '" . $param->getName() . "' in '" .
                    "class '" . $definition->getClassName() . "' is missing or it is not a class!"
                );
            }

            $definition->addRequiredConstructorArgument($paramClass);
            $this->resolve($paramClass);
        }
    }

    private function resolveAnnotatedProperties(ClassDefinition $definition): void
    {
        $class = new ReflectionClass($definition->getClassName());

        foreach ($class->getProperties() as $property) {
            /** @var Inject $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);
            if ($annotation === null) {
                continue;
            }

            if ($property->isStatic()) {
                throw new ContainerException(
                    "Property '" . $class->getName() . "::$" . $property->getName() .
                    "' is static and can't be injected on!"
                );
            }

            $propertyClass = $this->typeHintReader->getPropertyClass($property);
            if ($propertyClass === null) {
                throw new ContainerException(
                    "'@var' PHPDoc comment for property '" . $definition->getClassName() . "::$" . $property->getName() .
                    "' is missing or it is not a class!"
                );
            }

            $definition->addProperty($property->getName(), $propertyClass);
            $this->resolve($propertyClass);
        }
    }

    private function setAnnotationReader(): void
    {
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Zen\Annotation');
    }
}
