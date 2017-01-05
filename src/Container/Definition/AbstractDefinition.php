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

    public function __construct(string $id, string $hash)
    {
        $this->id = $id;
        $this->hash = $hash;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    protected function getEntryToPhp($hash): string
    {
        return "\$this->singletonEntries['$hash'] ?? \$this->$hash()";
    }
}
