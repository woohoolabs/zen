<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;

class StubContainerConfig extends AbstractContainerConfig
{
    /** @var WildcardHintInterface[] */
    private array $wildcardHints;

    /**
     * @param EntryPointInterface[]|string[]     $entryPoints
     * @param DefinitionHintInterface[]|string[] $definitionHints
     * @param WildcardHintInterface[]            $wildcardHints
     */
    public function __construct(array $entryPoints = [], array $definitionHints = [], array $wildcardHints = [])
    {
        $this->entryPoints = $entryPoints;
        $this->definitionHints = $definitionHints;
        $this->wildcardHints = $wildcardHints;
        parent::__construct();
    }

    /**
     * @return EntryPointInterface[]|string[]
     */
    protected function getEntryPoints(): array
    {
        return $this->entryPoints;
    }

    /**
     * @return DefinitionHintInterface[]|string[]
     */
    protected function getDefinitionHints(): array
    {
        return $this->definitionHints;
    }

    /**
     * @return WildcardHintInterface[]
     */
    protected function getWildcardHints(): array
    {
        return $this->wildcardHints;
    }
}
