<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;

class StubContainerConfig extends AbstractContainerConfig
{
    /**
     * @var WildcardHintInterface[]
     */
    private $wildcardHints;

    public function __construct(array $entryPoints = [], array $definitionHints = [], array $wildcardHints = [])
    {
        $this->entryPoints = $entryPoints;
        $this->definitionHints = $definitionHints;
        $this->wildcardHints = $wildcardHints;
        parent::__construct();
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
