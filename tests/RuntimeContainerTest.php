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

        $this->assertFalse($container->has("TestContainerEntry"));
    }

    /**
     * @test
     */
    public function hasReturnsTrue()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container2");

        $this->assertTrue($container->has(ConstructorA::class));
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
            "Container4");

        $this->assertInstanceOf(ConstructorA::class, $container->get(ConstructorA::class));
        $this->assertNotSame($container->get(ConstructorA::class), $container->get(ConstructorA::class));
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry()
    {
        $container = $this->createRuntimeContainer(ConstructorA::class, [], "Container5");

        $this->assertSame($container->get(ConstructorA::class), $container->get(ConstructorA::class));
    }

    private function createRuntimeContainer(string $entryPoint, array $definitionsHints, string $className): ContainerInterface
    {
        return new RuntimeContainer(
            new StubCompilerConfig(
                [
                    new StubContainerConfig([$entryPoint], $definitionsHints)
                ],
                "WoohooLabs\\Zen",
                $className
            )
        );
    }
}
