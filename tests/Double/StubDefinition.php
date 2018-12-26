<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use function str_replace;

class StubDefinition implements DefinitionInterface
{
    /**
     * @var bool
     */
    private $isEntryPoint;

    /**
     * @var bool
     */
    private $isAutoloaded;

    /**
     * @var bool
     */
    private $isFileBased;

    public function __construct(bool $isEntryPoint = false, bool $isAutoloaded = false, bool $isFileBased = false)
    {
        $this->isEntryPoint = $isEntryPoint;
        $this->isAutoloaded = $isAutoloaded;
        $this->isFileBased = $isFileBased;
    }

    public function getId(string $parentId): string
    {
        return self::class;
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

    public function isEntryPoint(): bool
    {
        return $this->isEntryPoint;
    }

    public function isAutoloaded(): bool
    {
        return $this->isAutoloaded;
    }

    public function isFileBased(): bool
    {
        return $this->isFileBased;
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
