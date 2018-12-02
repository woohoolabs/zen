<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\Tests\Double\StubContainer;
use WoohooLabs\Zen\Tests\Double\StubContainerEntry;
use WoohooLabs\Zen\Tests\Fixture\Container\ContainerWithInjectedProperty;

class AbstractCompiledContainerTest extends TestCase
{
    /**
     * @test
     */
    public function hasReturnsFalse()
    {
        $container = $this->createStubContainer();

        $hasEntry = $container->has("TestContainerEntry");

        $this->assertFalse($hasEntry);
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $container = $this->createStubContainer();

        $hasEntry = $container->has(StubContainerEntry::class);

        $this->assertTrue($hasEntry);
    }

    /**
     * @test
     */
    public function getThrowsNotFoundException()
    {
        $container = $this->createStubContainer();

        $this->expectException(NotFoundException::class);

        $container->get("TestContainerEntry");
    }

    /**
     * @test
     */
    public function getReturnsPrototypeEntry()
    {
        $container = $this->createStubContainer();

        $entry1 = $container->get(StubContainerEntry::class);
        $entry2 = $container->get(StubContainerEntry::class);

        $this->assertInstanceOf(StubContainerEntry::class, $entry1);
        $this->assertInstanceOf(StubContainerEntry::class, $entry2);
        $this->assertNotSame($entry1, $entry2);
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry()
    {
        $container = $this->createStubContainer(true);

        $entry = $container->get(StubContainerEntry::class);

        $this->assertSame($container->get(StubContainerEntry::class), $entry);
    }

    /**
     * @test
     */
    public function setProperty()
    {
        $container = new ContainerWithInjectedProperty();

        $property = $container->getProperty();

        $this->assertTrue($property);
    }

    private function createStubContainer(bool $isSingleton = false): ContainerInterface
    {
        return new StubContainer($isSingleton);
    }
}
