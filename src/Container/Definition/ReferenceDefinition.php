<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;

class ReferenceDefinition extends AbstractDefinition
{
    /**
     * @var string
     */
    private $referencedId;

    public static function singleton(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ReferenceDefinition {
        return new self(
            $referrerId,
            $referencedId,
            true,
            $isEntryPoint,
            $isAutoloaded,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
    }

    public static function prototype(
        string $referrerId,
        string $referencedId,
        bool $isEntryPoint = false,
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ): ReferenceDefinition {
        return new self(
            $referrerId,
            $referencedId,
            false,
            $isEntryPoint,
            $isAutoloaded,
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
        bool $isAutoloaded = false,
        bool $isFileBased = false,
        int $singletonReferenceCount = 0,
        int $prototypeReferenceCount = 0
    ) {
        parent::__construct(
            $referrerId,
            $isSingleton,
            $isEntryPoint,
            $isAutoloaded,
            $isFileBased,
            $singletonReferenceCount,
            $prototypeReferenceCount
        );
        $this->referencedId = $referencedId;
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function getClassDependencies(): array
    {
        return [
            $this->referencedId,
        ];
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     * @return mixed
     */
    public function instantiate($instantiation, $parentId)
    {
        if ($this->isSingletonCheckEliminable($parentId)) {
            return $instantiation->getDefinition($this->referencedId)->instantiate($instantiation, $this->id);
        }

        return $instantiation->getSingletonEntry($this->id) ?? $instantiation->setSingletonEntry(
            $this->id,
            $instantiation->getDefinition($this->referencedId)->instantiate($instantiation, $this->id)
        );
    }

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        $indent = $this->indent($indentationLevel);

        $code = "";

        if ($this->isAutoloadingInlinable($parentId, $inline)) {
            $code .= $this->includeRelatedClasses(
                $compilation->getAutoloadConfig(),
                $compilation->getDefinitions(),
                $this->id,
                $indentationLevel
            );
            $code .= "\n";
        }

        if ($inline === false) {
            $code .= "${indent}return ";
        }

        if ($this->isSingletonCheckEliminable($parentId) === false) {
            $code .= "\$this->singletonEntries['{$this->id}'] = ";
        }

        $definition = $compilation->getDefinition($this->referencedId);

        $code .= $this->compileEntryReference($definition, $compilation, $indentationLevel);

        if ($inline === false) {
            $code .= ";\n";
        }

        return $code;
    }
}
