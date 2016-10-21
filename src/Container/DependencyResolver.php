<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Container;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Config\CompilerConfig;
use WoohooLabs\Dicone\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Dicone\Container\Definition\ClassDefinition;
use WoohooLabs\Dicone\Container\Definition\DefinitionInterface;
use WoohooLabs\Dicone\Container\Definition\SelfDefinition;
use WoohooLabs\Dicone\Exception\ConstructorParamTypeHintException;
use WoohooLabs\Dicone\Exception\ContainerConfigException;
use WoohooLabs\Dicone\Exception\PropertyTypeHintException;

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
    public function __construct(CompilerConfig $config, array $definitionHints)
    {
        $this->compilerConfig = $config;
        $this->definitionHints = $definitionHints;
        $this->definitions = [];
        $this->setAnnotationReader();
        $this->typeHintReader = new PhpDocReader();
    }

    public function resolve(string $id)
    {
        if (empty($this->definitions)) {
            $this->definitions[$this->compilerConfig->getContainerHash()] = new SelfDefinition($this->compilerConfig->getContainerFqcn());
        }

        if (isset($this->definitions[$id])) {
            return;
        }

        if (isset($this->definitionHints[$id])) {
            $this->definitions[$id] = $this->definitionHints[$id]->toDefinition($id);

            if ($this->definitions[$id] instanceof ClassDefinition) {
                $this->resolve($this->definitions[$id]->getId());
            }

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
        } catch (\ReflectionException $e) {
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
        $this->annotationReader->addNamespace('WoohooLabs\Dicone\Annotation');
    }
}
