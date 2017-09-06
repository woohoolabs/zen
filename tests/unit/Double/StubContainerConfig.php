<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;

class StubContainerConfig extends AbstractContainerConfig
{
    /**
     * @var array
     */
    private $entryPoints;

    /**
     * @var DefinitionHintInterface[]
     */
    private $definitionHints;

    /**
     * @var WildcardHintInterface[]
     */
    private $wildcardHints;

    public function __construct(array $entryPoints = [], array $definitionHints = [], array $wildcardHints = [])
    {
        $this->entryPoints = $entryPoints;
        $this->definitionHints = $definitionHints;
        $this->wildcardHints = $wildcardHints;
    }

    protected function getEntryPoints(): array
    {
        return $this->entryPoints;
    }

    protected function getDefinitionHints(): array
    {
        return $this->definitionHints;
    }

    protected function getWildcardHints(): array
    {
        return $this->wildcardHints;
    }
}
