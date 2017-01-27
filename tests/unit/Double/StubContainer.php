<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\AbstractContainer;

class StubContainer extends AbstractContainer
{
    /**
     * @var bool
     */
    private $isSingleton;

    /**
     * @var string[]
     */
    protected static $entryPoints = [
        StubContainerEntry::class => 'WoohooLabs__Zen__Tests__Unit__Double__StubContainerEntry',
    ];

    public function __construct(bool $isSingleton)
    {
        $this->isSingleton = $isSingleton;
    }

    protected function WoohooLabs__Zen__Tests__Unit__Double__StubContainerEntry()
    {
        $entry = new StubContainerEntry();

        if ($this->isSingleton) {
            $this->singletonEntries['WoohooLabs\Zen\Tests\Unit\Double\StubContainerEntry'] = $entry;
        }

        return $entry;
    }
}
