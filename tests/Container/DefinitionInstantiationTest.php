<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;

class DefinitionInstantiationTest extends TestCase
{
    /**
     * @test
     */
    public function construct()
    {
        $instantiation = $this->createDefinitionInstantiation([]);

        $this->assertInstanceOf(RuntimeContainer::class, $instantiation->container);
        $this->assertEmpty($instantiation->definitions);
        $this->assertEmpty($instantiation->singletonEntries);
    }

    private function createDefinitionInstantiation(array $definitions): DefinitionInstantiation
    {
        $singletonEntries = [];

        return new DefinitionInstantiation(
            new RuntimeContainer(new DummyCompilerConfig()),
            $definitions,
            $singletonEntries
        );
    }
}
