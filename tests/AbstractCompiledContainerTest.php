<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\Tests\Double\StubContainerEntry;
use WoohooLabs\Zen\Tests\Double\StubPrototypeContainer;
use WoohooLabs\Zen\Tests\Double\StubSingletonContainer;
use WoohooLabs\Zen\Tests\Fixture\Container\ContainerWithInjectedProperty;

class AbstractCompiledContainerTest extends TestCase
{
    /**
     * @test
     */
    public function hasReturnsFalse(): void
    {
        $container = $this->createStubSingletonContainer();

        $hasEntry = $container->has("TestContainerEntry");

        $this->assertFalse($hasEntry);
    }

    /**
     * @test
     */
    public function hasReturnsTrue(): void
    {
        $container = $this->createStubSingletonContainer();

        $hasEntry = $container->has(StubContainerEntry::class);

        $this->assertTrue($hasEntry);
    }

    /**
     * @test
     */
    public function getThrowsNotFoundException(): void
    {
        $container = $this->createStubSingletonContainer();

        $this->expectException(NotFoundException::class);

        $container->get("TestContainerEntry");
    }

    /**
     * @test
     */
    public function getReturnsPrototypeEntry(): void
    {
        $container = $this->createStubPrototypeContainer();

        $entry1 = $container->get(StubContainerEntry::class);
        $entry2 = $container->get(StubContainerEntry::class);

        $this->assertInstanceOf(StubContainerEntry::class, $entry1);
        $this->assertInstanceOf(StubContainerEntry::class, $entry2);
        $this->assertNotSame($entry1, $entry2);
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry(): void
    {
        $container = $this->createStubSingletonContainer();

        $entry1 = $container->get(StubContainerEntry::class);
        $entry2 = $container->get(StubContainerEntry::class);

        $this->assertInstanceOf(StubContainerEntry::class, $entry1);
        $this->assertInstanceOf(StubContainerEntry::class, $entry2);
        $this->assertSame($entry1, $entry2);
    }

    /**
     * @test
     */
    public function setProperty(): void
    {
        $container = new ContainerWithInjectedProperty();

        $property = $container->getProperty();

        $this->assertInstanceOf(stdClass::class, $property);
    }

    private function createStubSingletonContainer(): ContainerInterface
    {
        return new StubSingletonContainer();
    }

    private function createStubPrototypeContainer(): ContainerInterface
    {
        return new StubPrototypeContainer();
    }
}
