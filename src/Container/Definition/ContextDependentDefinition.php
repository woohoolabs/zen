<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Exception\ContainerException;

class ContextDependentDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $referrerId;

    /**
     * @var DefinitionInterface|null
     */
    private $defaultDefinition;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

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

    public function isAutoloaded(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isAutoloaded($parentId);
    }

    public function isFileBased(string $parentId = ""): bool
    {
        return $this->getDefinition($parentId)->isFileBased($parentId);
    }

    public function getReferenceCount(string $parentId = ""): int
    {
        return $this->getDefinition($parentId)->getReferenceCount($parentId);
    }

    public function increaseReferenceCount(string $parentId = ""): DefinitionInterface
    {
        return $this->getDefinition($parentId)->increaseReferenceCount($parentId);
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
        ];
    }

    public function compile(DefinitionCompilation $compilation, string $parentId, int $indentationLevel, bool $inline = false): string
    {
        return $this->getDefinition($parentId)->compile($compilation, $parentId, $indentationLevel, $inline);
    }

    private function getDefinition(string $parentId): DefinitionInterface
    {
        if (isset($this->definitions[$parentId]) === false && $this->defaultDefinition === null) {
            throw new ContainerException(
                "The Context-Dependent definition with the '{$this->referrerId}' ID can't be injected for the '{$parentId}' class!"
            );
        }

        return $this->definitions[$parentId] ?? $this->defaultDefinition;
    }
}
