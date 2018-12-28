<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use function str_replace;

abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var bool
     */
    protected $entryPoint;

    /**
     * @var bool
     */
    protected $fileBased;

    /**
     * @var int
     */
    protected $referenceCount = 0;

    public function __construct(string $id, string $scope, bool $isEntryPoint, bool $fileBased)
    {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->scope = $scope;
        $this->entryPoint = $isEntryPoint;
        $this->fileBased = $fileBased;
    }

    public function getId(string $parentId): string
    {
        return $this->id;
    }

    public function getHash(string $parentId): string
    {
        return $this->hash;
    }

    public function getScope(string $parentId): string
    {
        return $this->scope;
    }

    public function isEntryPoint(): bool
    {
        return $this->entryPoint;
    }

    public function isFileBased(): bool
    {
        return $this->fileBased;
    }

    public function getReferenceCount(): int
    {
        return $this->referenceCount;
    }

    public function increaseReferenceCount(): void
    {
        $this->referenceCount++;
    }

    protected function getEntryToPhp(string $id, string $hash, string $scope, bool $isFileBased): string
    {
        if ($isFileBased) {
            if ($scope === "singleton") {
            }

            return "require __DIR__ . '/$hash.php'";
        }

        if ($scope === "singleton") {
        }

        return "\$this->$hash()";
    }

    protected function hash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
