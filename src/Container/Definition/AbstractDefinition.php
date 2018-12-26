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

    public function __construct(string $id, string $scope)
    {
        $this->id = $id;
        $this->hash = $this->hash($id);
        $this->scope = $scope;
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

    protected function getEntryToPhp(string $id, string $hash, string $scope): string
    {
        if ($scope === "singleton") {
            return "\$this->singletonEntries['$id'] ?? \$this->$hash()";
        }

        return "\$this->$hash()";
    }

    protected function hash(string $id): string
    {
        return str_replace("\\", "__", $id);
    }
}
