<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use ReflectionException;
use WoohooLabs\Zen\Annotation\Inject;
use WoohooLabs\Zen\Config\CompilerConfig;
use WoohooLabs\Zen\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Exception\ConstructorParamTypeHintException;
use WoohooLabs\Zen\Exception\ContainerConfigException;
use WoohooLabs\Zen\Exception\PropertyTypeHintException;

class DependencyResolver
{
    /**
     * @var CompilerConfig
     */
    private $compilerConfig;

    /**
     * @var DefinitionHint[]
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
     * @var PhpDocReader|null
     */
    private $typeHintReader;

    /**
     * @param DefinitionHint[] $definitionHints
     */
    public function __construct(CompilerConfig $compilerConfig, array $definitionHints)
    {
        $this->compilerConfig = $compilerConfig;
        $this->definitionHints = $definitionHints;
        $this->definitions = [
            $this->compilerConfig->getContainerFqcn() => new SelfDefinition($this->compilerConfig->getContainerFqcn())
        ];
        $this->setAnnotationReader();
        $this->typeHintReader = new PhpDocReader();
    }

    public function resolve(string $id)
    {
        if (isset($this->definitions[$id])) {
            return;
        }

        if (isset($this->definitionHints[$id])) {
            $this->definitions[$id] = $this->definitionHints[$id]->toDefinition($id);
            $this->resolve($this->definitions[$id]->getId());

            return;
        }

        $this->definitions[$id] = new ClassDefinition($id);

        if ($this->compilerConfig->useConstructorInjection()) {
            $this->resolveConstructorArguments($this->definitions[$id]);
        }

        if ($this->compilerConfig->usePropertyInjection()) {
            $this->resolveAnnotatedProperties($this->definitions[$id]);
        }

        return;
    }

    /**
     * @return DefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    private function resolveConstructorArguments(ClassDefinition $definition)
    {
        try {
            $reflectionClass = new ReflectionClass($definition->getClassName());
        } catch (ReflectionException $e) {
            throw new ContainerConfigException("Class '" . $definition->getClassName() . "' does not exists!");
        }

        if ($reflectionClass->getConstructor() === null) {
            return;
        }

        foreach ($reflectionClass->getConstructor()->getParameters() as $param) {
            if ($param->isOptional()) {
                $definition->addOptionalConstructorParam($param->getDefaultValue());
                continue;
            }

            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                throw new ConstructorParamTypeHintException($definition->getClassName(), $param->getName());
            }

            $definition->addRequiredConstructorParam($paramClass);
            $this->resolve($paramClass);
        }
    }

    private function resolveAnnotatedProperties(ClassDefinition $definition)
    {
        $class = new ReflectionClass($definition->getClassName());

        foreach ($class->getProperties() as $property) {
            /** @var Inject $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);
            if ($annotation === null) {
                continue;
            }

            $propertyClass = $this->typeHintReader->getPropertyClass($property);
            if ($propertyClass === null) {
                throw new PropertyTypeHintException($definition->getClassName(), $property->getName());
            }

            $definition->addProperty($property->getName(), $propertyClass);
            $this->resolve($propertyClass);
        }
    }

    private function setAnnotationReader()
    {
        AnnotationRegistry::registerFile(realpath(__DIR__ . '/../Annotation/Inject.php'));
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Zen\Annotation');
    }
}
