<?php
namespace WoohooLabs\Zen\Tests\Unit\Fixture\Container;

use WoohooLabs\Zen\AbstractContainer;
use WoohooLabs\Zen\Tests\Unit\Double\StubContainerEntry;

class ContainerWithInjectedProperty extends AbstractContainer
{
    protected function A()
    {
        return true;
    }

    public function getProperty(): bool
    {
        $entry = new StubContainerEntry();
        $this->setProperties($entry, ["a" => "A"]);

        return $entry->getA();
    }
}
