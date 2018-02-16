<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $scope;

    public function __construct(string $id, string $hash, string $scope)
    {
        $this->id = $id;
        $this->hash = $hash;
        $this->scope = $scope;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getScope(): string
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
}
