<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

class ReferenceDefinition extends AbstractDefinition
{
    private string $referencedId;

    public static function singleton(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ReferenceDefinition {
        return new self(
            $referrerId,
            $referencedId,
            true,
            $isEntryPoint,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
    }

    public static function prototype(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ReferenceDefinition {
        return new self(
            $referrerId,
            $referencedId,
            false,
            $isEntryPoint,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
    }

    public function __construct(
        string $referrerId,
        string $referencedId,
        bool $isSingleton = true,
        bool $isEntryPoint = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ) {
        parent::__construct(
            $referrerId,
            $isSingleton,
            $isEntryPoint,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
        $this->referencedId = $referencedId;
    }

    public function isDefinitionInlinable(string $parentId = ""): bool
    {
        if ($this->isEntryPoint($parentId) === false || $this->isFileBased($parentId)) {
            return false;
        }

        if ($this->getSingletonReferenceCount() + $this->getPrototypeReferenceCount() > 0) {
            return false;
        }

        return true;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    /**
     * @return string[]
     */
    public function getClassDependencies(): array
    {
        return [
            $this->referencedId,
        ];
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     */
    public function instantiate($instantiation, $parentId): mixed
    {
        if ($this->singleton === false) {
            return $instantiation->definitions[$this->referencedId]->instantiate($instantiation, $this->id);
        }

        return $instantiation->singletonEntries[$this->id] ?? $instantiation->singletonEntries[$this->id] =
            $instantiation->definitions[$this->referencedId]->instantiate($instantiation, $this->id);
    }

    /**
     * @param string[] $preloadedClasses
     */
    public function compile(
        DefinitionCompilation $compilation,
        string $parentId,
        int $indentationLevel,
        bool $inline = false,
        array $preloadedClasses = []
    ): string {
        $indent = $this->indent($indentationLevel);

        $code = "";

        if ($inline === false) {
            $code .= "${indent}return ";
        }

        if ($this->isSingletonCheckEliminable($parentId) === false) {
            $code .= "\$this->singletonEntries['{$this->id}'] = ";
        }

        $definition = $compilation->getDefinition($this->referencedId);

        $code .= $this->compileEntryReference($definition, $compilation, $indentationLevel, $preloadedClasses);

        if ($inline === false) {
            $code .= ";\n";
        }

        return $code;
    }
}
