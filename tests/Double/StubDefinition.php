<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

class StubDefinition implements DefinitionInterface
{
    /**
     * @var bool
     */
    private $isAutoloaded;

    public function __construct(bool $isAutoloaded = false)
    {
        $this->isAutoloaded = $isAutoloaded;
    }

    public function getId(string $parentId): string
    {
        return StubDefinition::class;
    }

    public function getHash(string $parentId): string
    {
        return str_replace("\\", "__", $this->getId(""));
    }

    public function getScope(string $parentId): string
    {
        return "";
    }

    public function needsDependencyResolution(): bool
    {
        return false;
    }

    public function resolveDependencies(): DefinitionInterface
    {
        return $this;
    }

    public function isAutoloaded(): bool
    {
        return $this->isAutoloaded;
    }

    public function getClassDependencies(): array
    {
        return [];
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function toPhpCode(array $definitions): string
    {
        return "        // This is a dummy definition.\n";
    }
}
