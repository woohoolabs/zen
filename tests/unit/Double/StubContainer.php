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

    public function __construct(bool $isSingleton)
    {
        $this->isSingleton = $isSingleton;
    }

    protected function WoohooLabs__Zen__Tests__Unit__Double__StubContainerEntry()
    {
        $entry = new StubContainerEntry();

        if ($this->isSingleton) {
            $this->singletonEntries[$this->getHash(StubContainerEntry::class)] = $entry;
        }

        return $entry;
    }
}
