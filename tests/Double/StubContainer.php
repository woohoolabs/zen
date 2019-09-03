<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\AbstractCompiledContainer;

class StubContainer extends AbstractCompiledContainer
{
    private bool $isSingleton;
    /** @var string[] */
    protected static $entryPoints = [
        StubContainerEntry::class => "WoohooLabs__Zen__Tests__Double__StubContainerEntry",
    ];

    public function __construct(bool $isSingleton)
    {
        $this->isSingleton = $isSingleton;
    }

    protected function WoohooLabs__Zen__Tests__Double__StubContainerEntry()
    {
        $entry = new StubContainerEntry();

        if ($this->isSingleton) {
            $this->singletonEntries['WoohooLabs\Zen\Tests\Double\StubContainerEntry'] = $entry;
        }

        return $entry;
    }
}
