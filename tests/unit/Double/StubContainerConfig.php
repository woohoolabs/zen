<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Config\AbstractContainerConfig;

class StubContainerConfig extends AbstractContainerConfig
{
    /**
     * @var array
     */
    private $entryPoints;

    public function __construct(array $entryPoints)
    {
        $this->entryPoints = $entryPoints;
    }

    protected function getEntryPoints(): array
    {
        return $this->entryPoints;
    }

    protected function getDefinitionHints(): array
    {
        return [];
    }

    protected function getWildcardHints(): array
    {
        return [];
    }
}
