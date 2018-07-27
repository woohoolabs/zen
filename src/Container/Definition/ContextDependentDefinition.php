<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

class ContextDependentDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $referrerId;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    public function __construct(string $referrerId, array $definitions)
    {
        $this->referrerId = $referrerId;
        $this->definitions = $definitions;
    }

    public function getId(string $parentId): string
    {
        return $this->definitions[$parentId]->getId($parentId);
    }

    public function getHash(string $parentId): string
    {
        return $this->definitions[$parentId]->getHash($parentId);
    }

    public function getScope(string $parentId): string
    {
        return $this->definitions[$parentId]->getScope($parentId);
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function isAutoloaded(): bool
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

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        return "";
    }
}
