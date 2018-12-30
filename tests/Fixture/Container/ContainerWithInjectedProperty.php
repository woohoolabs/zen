<?php
namespace WoohooLabs\Zen\Tests\Fixture\Container;

use WoohooLabs\Zen\AbstractCompiledContainer;
use WoohooLabs\Zen\Tests\Double\StubContainerEntry;

class ContainerWithInjectedProperty extends AbstractCompiledContainer
{
    /**
     * @var string
     */
    protected $rootDirectory;

    public function __construct(string $rootDirectory = '')
    {
        $this->rootDirectory = $rootDirectory;
    }

    protected function A()
    {
        return true;
    }

    public function getProperty(): bool
    {
        $entry = new StubContainerEntry();
        $this->setClassProperties($entry, ['a' => $this->singletonEntries['A'] ?? $this->A()]);

        return $entry->getA();
    }
}
