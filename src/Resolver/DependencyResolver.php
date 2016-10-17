<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Resolver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Compiler\CompilerConfig;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Exception\ConstructorParamTypeHintException;
use WoohooLabs\Dicone\Exception\PropertyTypeHintException;

class DependencyResolver
{
    /**
     * @var CompilerConfig
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
    private $definitionItems = [];

    public function __construct(CompilerConfig $config)
    {
        $this->config = $config;
        $this->typeHintReader = new PhpDocReader();
    }

    /**
     * @return DefinitionItem[]
     */
    public function getDefinitionItems(): array
    {
        return $this->definitionItems;
    }

    public function resolve(string $className)
    {
        if (isset($this->definitionItems[$className])) {
            return;
        }

        $this->definitionItems[$className] = new DefinitionItem($className);

        if ($this->config->useConstructorTypeHints()) {
            $this->resolveConstructorDependencies($this->definitionItems[$className]);
        }

        if ($this->config->usePropertyAnnotation()) {
            $this->resolvePropertyAnnotationDependencies($this->definitionItems[$className]);
        }
    }

    public function addDefinitionItem(string $key, DefinitionItem $definitionItem)
    {
        $this->definitionItems[$key] = $definitionItem;
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
            /** @var Inject $annotation */
            $annotation = $this->getAnnotationReader()->getPropertyAnnotation($property, Inject::class);
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

    private function getAnnotationReader(): SimpleAnnotationReader
    {
        if ($this->annotationReader === null) {
            AnnotationRegistry::registerFile(realpath(__DIR__ . '/../Annotation/Inject.php'));
            $this->annotationReader = new SimpleAnnotationReader();
            $this->annotationReader->addNamespace('WoohooLabs\Dicone\Annotation');
        }

        return $this->annotationReader;
    }
}
