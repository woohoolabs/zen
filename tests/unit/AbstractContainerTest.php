<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\Tests\Unit\Double\StubContainer;
use WoohooLabs\Zen\Tests\Unit\Double\StubContainerEntry;
use WoohooLabs\Zen\Tests\Unit\Fixture\Container\ContainerWithInjectedProperty;

class AbstractContainerTest extends TestCase
{
    /**
     * @test
     */
    public function hasReturnsFalse()
    {
        $container = $this->createStubContainer();

        $this->assertFalse($container->has("TestContainerEntry"));
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $container = $this->createStubContainer();

        $this->assertTrue($container->has(StubContainerEntry::class));
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

        $this->assertInstanceOf(StubContainerEntry::class, $container->get(StubContainerEntry::class));
        $this->assertNotSame($container->get(StubContainerEntry::class), $container->get(StubContainerEntry::class));
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry()
    {
        $container = $this->createStubContainer(true);

        $this->assertSame($container->get(StubContainerEntry::class), $container->get(StubContainerEntry::class));
    }

    /**
     * @test
     */
    public function setProperty()
    {
        $container = new ContainerWithInjectedProperty();

        $this->assertTrue($container->getProperty());
    }

    private function createStubContainer(bool $isSingleton = false): ContainerInterface
    {
        return new StubContainer($isSingleton);
    }
}
