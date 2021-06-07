<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config;

use PHPUnit\Framework\TestCase;
use stdClass;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointC1;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\AClass;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\AInterface;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\BClass;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\BInterface;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassD;

use function dirname;

class AbstractContainerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function createEntryPointsWhenInvalidType(): void
    {
        $this->expectException(ContainerException::class);

        new StubContainerConfig(
            [
                new stdClass(),
            ],
            [],
            []
        );
    }

    /**
     * @test
     */
    public function createEntryPoints(): void
    {
        $config = new StubContainerConfig(
            [
                EntryPointA::class,
                new ClassEntryPoint(EntryPointC1::class),
            ],
            [],
            []
        );

        $entryPoints = $config->createEntryPoints();

        $this->assertEquals(
            [
                new ClassEntryPoint(EntryPointA::class),
                new ClassEntryPoint(EntryPointC1::class),
            ],
            $entryPoints
        );
    }

    /**
     * @test
     */
    public function createDefinitionHintsWhenInvalidType(): void
    {
        $this->expectException(ContainerException::class);

        new StubContainerConfig(
            [
            ],
            [
                "stdClass" => 0,
            ],
            []
        );
    }

    /**
     * @test
     */
    public function createDefinitionHintsWithoutWildcardHints(): void
    {
        $config = new StubContainerConfig(
            [
            ],
            [
                EntryPointA::class => EntryPointA::class,
                EntryPointC1::class => new DefinitionHint(EntryPointC1::class),
            ],
            []
        );

        $definitionHints = $config->createDefinitionHints();

        $this->assertEquals(
            [
                EntryPointA::class => new DefinitionHint(EntryPointA::class),
                EntryPointC1::class => new DefinitionHint(EntryPointC1::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function createDefinitionHintsWithOnlyWildcardHints(): void
    {
        $config = new StubContainerConfig(
            [
            ],
            [
            ],
            [
                new WildcardHint(
                    dirname(__DIR__) . "/Fixture/DependencyGraph/Wildcard/",
                    'WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\*Interface',
                    'WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\*Class',
                    "singleton"
                ),
            ]
        );

        $definitionHints = $config->createDefinitionHints();

        $this->assertEquals(
            [
                AInterface::class => new DefinitionHint(AClass::class),
                BInterface::class => new DefinitionHint(BClass::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function createDefinitionHintsWithHintsAndWildcardHints(): void
    {
        $config = new StubContainerConfig(
            [
            ],
            [
                ClassC::class => new DefinitionHint(ClassC::class),
                AInterface::class => new DefinitionHint(AClass::class),
                BInterface::class => new DefinitionHint(BClass::class),
                ClassD::class => new DefinitionHint(ClassD::class),
            ],
            [
                new WildcardHint(
                    dirname(__DIR__) . "/Fixture/DependencyGraph/Wildcard/",
                    'WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\*Interface',
                    'WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\*Class',
                    "prototype"
                ),
            ]
        );

        $definitionHints = $config->createDefinitionHints();

        $this->assertEquals(
            [
                ClassC::class => new DefinitionHint(ClassC::class),
                AInterface::class => new DefinitionHint(AClass::class, "prototype"),
                BInterface::class => new DefinitionHint(BClass::class, "prototype"),
                ClassD::class => new DefinitionHint(ClassD::class),
            ],
            $definitionHints
        );
    }
}
