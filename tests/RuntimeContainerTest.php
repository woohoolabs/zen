<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorA;

class RuntimeContainerTest extends TestCase
{
    /**
     * @test
     */
    public function hasReturnsFalse()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container1");

        $hasEntry = $container->has("TestContainerEntry");

        $this->assertFalse($hasEntry);
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container2");

        $hasEntry = $container->has(ConstructorA::class);

        $this->assertTrue($hasEntry);
    }

    /**
     * @test
     */
    public function getThrowsNotFoundException()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container3");

        $this->expectException(NotFoundException::class);

        $container->get("TestContainerEntry");
    }

    /**
     * @test
     */
    public function getReturnsPrototypeEntry()
    {
        $container = $this->createRuntimeContainer(
            ConstructorA::class,
            [
                ConstructorA::class => DefinitionHint::prototype(ConstructorA::class),
            ],
            "Container4"
        );

        $entry1 = $container->get(ConstructorA::class);
        $entry2 = $container->get(ConstructorA::class);

        $this->assertInstanceOf(ConstructorA::class, $entry1);
        $this->assertInstanceOf(ConstructorA::class, $entry2);
        $this->assertNotSame($entry1, $entry2);
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container5");

        $entry = $container->get(ConstructorA::class);

        $this->assertSame($container->get(ConstructorA::class), $entry);
    }

    private function createRuntimeContainer(string $entryPoint, array $definitionsHints, string $className): ContainerInterface
    {
        return new RuntimeContainer(
            new StubCompilerConfig(
                [
                    new StubContainerConfig([$entryPoint], $definitionsHints),
                ],
                "WoohooLabs\\Zen",
                $className
            )
        );
    }
}
