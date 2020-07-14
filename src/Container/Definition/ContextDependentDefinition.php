<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Exception\ContainerException;

use function array_key_exists;

class ContextDependentDefinition implements DefinitionInterface
{
    private string $referrerId;
    private ?DefinitionInterface $defaultDefinition;
    /** @var DefinitionInterface[] */
    private array $definitions;

    /**
     * @param DefinitionInterface[] $contextDependentDefinitions
     */
    public function __construct(string $referrerId, ?DefinitionInterface $defaultDefinition, array $contextDependentDefinitions)
    {
        $this->referrerId = $referrerId;
        $this->defaultDefinition = $defaultDefinition;
        $this->definitions = $contextDependentDefinitions;
    }

    public function getId(string $parentId = ""): string
    {
        return $this->getDefinition($parentId)->getId($parentId);
    }

    public function getHash(string $parentId = ""): string
    {
        return $this->getDefinition($parentId)->getHash($parentId);
    }

    public function isSingleton(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isSingleton($parentId);
    }

    public function isEntryPoint(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isEntryPoint($parentId);
    }

    public function isFileBased(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isFileBased($parentId);
    }

    public function increaseReferenceCount(string $parentId, bool $isSingletonParent): DefinitionInterface
    {
        return $this->getDefinition($parentId)->increaseReferenceCount($parentId, $isSingletonParent);
    }

    public function isDefinitionInlinable(string $parentId = ""): bool
    {
        return false;
    }

    public function isSingletonCheckEliminable(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isSingletonCheckEliminable($parentId);
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
        ];
    }

    /**
     * @param DefinitionInstantiation $instantiation
     * @param string $parentId
     */
    public function instantiate($instantiation, $parentId): mixed
    {
        return $this->getDefinition($parentId)->instantiate($instantiation, $this->referrerId);
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
        return $this->getDefinition($parentId)->compile($compilation, $parentId, $indentationLevel, $inline, $preloadedClasses);
    }

    private function getDefinition(string $parentId): DefinitionInterface
    {
        if (array_key_exists($parentId, $this->definitions)) {
            return $this->definitions[$parentId];
        }

        if ($this->defaultDefinition !== null) {
            return $this->defaultDefinition;
        }

        throw new ContainerException(
            "The Context-Dependent definition with the '{$this->referrerId}' ID can't be injected for the '{$parentId}' class!"
        );
    }
}
