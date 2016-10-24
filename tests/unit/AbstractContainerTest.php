<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Tests\Unit\Double\StubContainer;
use WoohooLabs\Zen\Tests\Unit\Double\DummyContainerEntry;

class AbstractContainerTest extends TestCase
{
    /**
     * @test
     */
    public function hasReturnsFalse()
    {
        $container = $this->createContainer();

        $this->assertFalse($container->has("TestContainerEntry"));
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->has(DummyContainerEntry::class));
    }

    /**
     * @test
     */
    public function getThrowsNotFoundException()
    {
        $container = $this->createContainer();

        $this->expectException(NotFoundException::class);
        $container->get("TestContainerEntry");
    }

    /**
     * @test
     */
    public function getReturnsPrototypeEntry()
    {
        $container = $this->createContainer();

        $this->assertInstanceOf(DummyContainerEntry::class, $container->get(DummyContainerEntry::class));
        $this->assertNotSame($container->get(DummyContainerEntry::class), $container->get(DummyContainerEntry::class));
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry()
    {
        $container = $this->createContainer(true);

        $this->assertSame($container->get(DummyContainerEntry::class), $container->get(DummyContainerEntry::class));
    }

    private function createContainer(bool $isSingleton = false): ContainerInterface
    {
        return new StubContainer($isSingleton);
    }
}
