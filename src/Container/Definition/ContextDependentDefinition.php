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
     * @param DefinitionInterface|null $defaultDefinition
     * @param DefinitionInterface[] $contextDependentDefinitions
     */
    public function __construct(string $referrerId, ?DefinitionInterface $defaultDefinition, array $contextDependentDefinitions)
    {
        $this->referrerId = $referrerId;
        $this->defaultDefinition = $defaultDefinition;
        $this->definitions = $contextDependentDefinitions;
    }

    public function getId(string $parentId): string
    {
        return $this->getDefinition($parentId)->getId($parentId);
    }

    public function getHash(string $parentId): string
    {
        return $this->getDefinition($parentId)->getHash($parentId);
    }

    public function isSingleton(string $parentId): bool
    {
        return $this->getDefinition($parentId)->isSingleton($parentId);
    }

    public function isEntryPoint(): bool
    {
        return false;
    }

    public function isAutoloaded(): bool
    {
        return false;
    }

    public function isFileBased(): bool
    {
        return false;
    }

    public function getReferenceCount(): int
    {
        return 0;
    }

    public function increaseReferenceCount(): DefinitionInterface
    {
        return $this;
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

    public function compile(DefinitionCompilation $compilation): string
    {
        if ($this->defaultDefinition === null) {
            return <<<EOF
        throw new \WoohooLabs\Zen\Exception\ContainerException(
            'Context-Dependent Definition with "{$this->referrerId}" ID doesn\'t have a default value, therefore it cannot be retrieved directly!'
        );

EOF;
        }

        return $this->defaultDefinition->compile($compilation);
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
