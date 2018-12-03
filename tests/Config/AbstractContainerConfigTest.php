<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Compiler;

use stdClass;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\WildcardEntryPoint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointC1;

class AbstractContainerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function createEntryPointsWhenInvalidType()
    {
        $config = new StubContainerConfig(
            [
                new stdClass(),
            ],
            [],
            []
        );

        $this->expectException(ContainerException::class);

        $config->createEntryPoints();
    }

    /**
     * @test
     */
    public function createEntryPoints()
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
    public function createDefinitionHintsWhenInvalidType()
    {
        $config = new StubContainerConfig(
            [
            ],
            [
                "stdClass" => 0,
            ],
            []
        );

        $this->expectException(ContainerException::class);

        $config->createDefinitionHints();
    }

    /**
     * @test
     */
    public function createDefinitionHintsWithoutWildcardHints()
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
    public function createDefinitionHintsWithOnlyWildcardHints()
    {
        $config = new StubContainerConfig(
            [
            ],
            [
            ],
            [
                new WildcardHint("", "", "", ""),
            ]
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
}
