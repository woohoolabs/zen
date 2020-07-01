<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Exception\NotFoundException;

class StubContainer extends AbstractCompiledContainer
{
    private bool $isSingleton;
    /** @var string[] */
    protected array $entryPoints = [
        StubContainerEntry::class => "WoohooLabs__Zen__Tests__Double__StubContainerEntry",
    ];

    public function __construct(bool $isSingleton)
    {
        $this->isSingleton = $isSingleton;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->entryPoints);
    }

    public function get($id): mixed
    {
        return isset($this->entryPoints[$id]) ? $this->entryPoints[$id]() : $this->throwNotFoundException($id);
    }

    protected function WoohooLabs__Zen__Tests__Double__StubContainerEntry()
    {
        $entry = new StubContainerEntry();

        if ($this->isSingleton) {
            $this->singletonEntries['WoohooLabs\Zen\Tests\Double\StubContainerEntry'] = $entry;
        }

        return $entry;
    }

    private function throwNotFoundException($id)
    {
        throw new NotFoundException($id);
    }
}
