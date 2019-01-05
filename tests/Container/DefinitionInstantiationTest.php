<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use stdClass;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;

class DefinitionInstantiationTest extends TestCase
{
    /**
     * @test
     */
    public function getContainer()
    {
        $instantiation = $this->createDefinitionInstantiation([]);

        $container = $instantiation->getContainer();

        $this->assertInstanceOf(RuntimeContainer::class, $container);
    }

    /**
     * @test
     */
    public function getDefinition()
    {
        $instantiation = $this->createDefinitionInstantiation(["X\\A" => ClassDefinition::singleton("X\\A")]);

        $definition = $instantiation->getDefinition("X\\A");

        $this->assertEquals(ClassDefinition::singleton("X\\A"), $definition);
    }

    /**
     * @test
     */
    public function getSingletonEntryWhenNotPresent()
    {
        $instantiation = $this->createDefinitionInstantiation([]);

        $entry = $instantiation->getSingletonEntry("X\\A");

        $this->assertNull($entry);
    }

    /**
     * @test
     */
    public function setSingletonEntry()
    {
        $instantiation = $this->createDefinitionInstantiation([]);

        $entry = $instantiation->setSingletonEntry("X\\A", new stdClass());

        $this->assertSame($entry, $instantiation->getSingletonEntry("X\\A"));
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
