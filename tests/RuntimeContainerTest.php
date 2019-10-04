<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
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
    public function hasReturnsFalse(): void
    {
        $container = $this->createRuntimeContainer([ConstructorA::class], []);

        $hasEntry = $container->has("TestContainerEntry");

        $this->assertFalse($hasEntry);
    }

    /**
     * @test
     */
    public function hasReturnsTrue(): void
    {
        $container = $this->createRuntimeContainer([ConstructorA::class], []);

        $hasEntry1 = $container->has(ConstructorA::class);
        $hasEntry2 = $container->has(ConstructorA::class);

        $this->assertTrue($hasEntry1);
        $this->assertTrue($hasEntry2);
    }

    /**
     * @test
     */
    public function getThrowsNotFoundException(): void
    {
        $container = $this->createRuntimeContainer([ConstructorA::class], []);

        $this->expectException(NotFoundException::class);

        $container->get("TestContainerEntry");
    }

    /**
     * @test
     */
    public function getReturnsPrototypeEntry(): void
    {
        $container = $this->createRuntimeContainer(
            [ConstructorA::class],
            [
                ConstructorA::class => DefinitionHint::prototype(ConstructorA::class),
            ]
        );

        $entry1 = $container->get(ConstructorA::class);
        $entry2 = $container->get(ConstructorA::class);

        $this->assertInstanceOf(ConstructorA::class, $entry1);
        $this->assertNotSame($entry1, $entry2);
    }

    /**
     * @test
     */
    public function getReturnsSingletonEntry(): void
    {
        $container = $this->createRuntimeContainer([ConstructorA::class], []);

        $entry1 = $container->get(ConstructorA::class);
        $entry2 = $container->get(ConstructorA::class);

        $this->assertInstanceOf(ConstructorA::class, $entry1);
        $this->assertSame($entry1, $entry2);
    }

    /**
     * @param EntryPointInterface[]|string[] $entryPoints
     * @param DefinitionHintInterface[]      $definitionsHints
     */
    private function createRuntimeContainer(array $entryPoints, array $definitionsHints): RuntimeContainer
    {
        return new RuntimeContainer(
            new StubCompilerConfig(
                [
                    new StubContainerConfig($entryPoints, $definitionsHints),
                ]
            )
        );
    }
}
