<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Compiler;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PhpDocReader\PhpDocReader;
use ReflectionClass;
use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Definition\DefinitionHint;
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
     * @var DefinitionHint[]
     */
    private $definitionHints;

    /**
     * @var DefinitionItem[]
     */
    private $definitionItems;

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
        $this->config = $config;
        $this->definitionHints = $definitionHints;
        $this->definitionItems = [];
        $this->setAnnotationReader();
        $this->typeHintReader = new PhpDocReader();
    }

    public function resolve(string $className)
    {
        if (isset($this->definitionItems[$className])) {
            return;
        }

        if (isset($this->definitionHints[$className])) {
            $this->definitionItems[$className] = $this->definitionHints[$className]->toDefinitionItem();
        } else {
            $this->definitionItems[$className] = new DefinitionItem($className);
        }

        if ($this->config->useConstructorTypeHints()) {
            $this->resolveConstructorDependencies($this->definitionItems[$className]);
        }

        if ($this->config->usePropertyAnnotation()) {
            $this->resolvePropertyAnnotationDependencies($this->definitionItems[$className]);
        }

        return;
    }

    /**
     * @return DefinitionItem[]
     */
    public function getDefinitionItems(): array
    {
        return $this->definitionItems;
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
            $annotation = $this->annotationReader->getPropertyAnnotation($property, Inject::class);
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

    private function setAnnotationReader()
    {
        AnnotationRegistry::registerFile(realpath(__DIR__ . '/../Annotation/Inject.php'));
        $this->annotationReader = new SimpleAnnotationReader();
        $this->annotationReader->addNamespace('WoohooLabs\Dicone\Annotation');
    }
}
