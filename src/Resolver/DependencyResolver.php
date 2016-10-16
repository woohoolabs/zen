<?php
namespace WoohooLabs\Dicone\Resolver;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use WoohooLabs\Dicone\Compiler\CompilationConfig;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Exception\ConstructorParamTypeHintException;
use WoohooLabs\Dicone\Exception\PropertyTypeHintException;
use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class DependencyResolver
{
    /**
     * @var CompilationConfig
     */
    private $config;

    /**
     * @var AnnotationReader|null
     */
    private $annotationReader;

    /**
     * @var PhpDocReader|null
     */
    private $typeHintReader;

    /**
     * @var array
     */
    private $graph = [];

    public function __construct(CompilationConfig $config)
    {
        $this->config = $config;
        $this->typeHintReader = new PhpDocReader();
    }

    public function getDependencyGraph(): array
    {
        return $this->graph;
    }

    public function resolve(string $className)
    {
        if (isset($this->graph[$className])) {
            return;
        }

        $this->graph[$className] = new DefinitionItem($className);

        if ($this->config->useConstructorTypeHints()) {
            $this->resolveConstructorDependencies($this->graph[$className]);
        }

        if ($this->config->usePropertyAnnotation()) {
            $this->resolvePropertyAnnotationDependencies($this->graph[$className]);
        }
    }

    private function resolveConstructorDependencies(DefinitionItem $item)
    {
        $reflectionClass = new ReflectionClass($item->getClassName());

        if ($reflectionClass->getConstructor() === null) {
            return;
        }

        foreach ($reflectionClass->getConstructor()->getParameters() as $param) {
            if ($param->isOptional()) {
                $item->addOptionalConstructorParam($param->getDefaultValue());
                continue;
            }

            $paramClass = $this->typeHintReader->getParameterClass($param);
            if ($paramClass === null) {
                throw new ConstructorParamTypeHintException($item->getClassName(), $param->getName());
            }

            $item->addRequiredConstructorParam($paramClass);
            $this->resolve($paramClass);
        }
    }

    private function resolvePropertyAnnotationDependencies(DefinitionItem $item)
    {
        $class = new ReflectionClass($item->getClassName());

        foreach ($class->getProperties() as $property) {
            /** @var Dependency $annotation */
            $annotation = $this->getAnnotationReader()->getPropertyAnnotation($property, Dependency::class);
            if ($annotation === null) {
                continue;
            }

            $propertyClass = $this->typeHintReader->getPropertyClass($property);
            if ($propertyClass === null) {
                throw new PropertyTypeHintException($item->getClassName(), $property->getName());
            }

            $item->addProperty($property->getName(), $propertyClass);
            $this->resolve($propertyClass);
        }
    }

    private function getAnnotationReader(): AnnotationReader
    {
        if ($this->annotationReader === null) {
            $this->annotationReader = new AnnotationReader();

            AnnotationRegistry::registerFile(__DIR__ . "/Annotation/Dependency.php");
        }

        return $this->annotationReader;
    }
}
