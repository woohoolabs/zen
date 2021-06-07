<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;

class AbstractCompilerConfigTest extends TestCase
{
    /**
     * @test
     */
    public function useConstructorInjection(): void
    {
        $config = new StubCompilerConfig([], "", "", true, false);

        $useConstructorInjection = $config->useConstructorInjection();

        $this->assertEquals(true, $useConstructorInjection);
    }

    /**
     * @test
     */
    public function usePropertyInjection(): void
    {
        $config = new StubCompilerConfig([], "", "", true, false);

        $usePropertyInjection = $config->usePropertyInjection();

        $this->assertEquals(false, $usePropertyInjection);
    }

    /**
     * @test
     */
    public function getContainerHash(): void
    {
        $config = new StubCompilerConfig([], "A\\B\\C", "D");

        $containerHash = $config->getContainerHash();

        $this->assertEquals("A__B__C__D", $containerHash);
    }

    /**
     * @test
     */
    public function getFileBasedDefinitionConfig(): void
    {
        $config = new DummyCompilerConfig();

        $fileBasedDefinitionConfig = $config->getFileBasedDefinitionConfig();

        $this->assertEquals(FileBasedDefinitionConfig::disabledGlobally(), $fileBasedDefinitionConfig);
    }

    /**
     * @test
     */
    public function getContainerFqcn(): void
    {
        $config = new StubCompilerConfig(
            [],
            "A\\B\\C",
            "Container"
        );

        $containerFqcn = $config->getContainerFqcn();

        $this->assertEquals("A\\B\\C\\Container", $containerFqcn);
    }

    /**
     * @test
     */
    public function getEntryPointMap(): void
    {
        $config = new StubCompilerConfig(
            [
                new StubContainerConfig(
                    [
                        ClassEntryPoint::create("X\\A"),
                    ]
                ),
                new StubContainerConfig(
                    [
                        ClassEntryPoint::create("X\\B"),
                    ]
                ),
                new StubContainerConfig(
                    [
                        ClassEntryPoint::create("X\\A")
                            ->fileBased(),
                    ]
                ),
            ]
        );

        $entryPointMap1 = $config->getEntryPointMap();
        $entryPointMap2 = $config->getEntryPointMap();

        $this->assertEquals(
            [
                ContainerInterface::class => new ClassEntryPoint(ContainerInterface::class),
                "" => new ClassEntryPoint(""),
                "X\\A" => ClassEntryPoint::create("X\\A"),
                "X\\B" => ClassEntryPoint::create("X\\B"),
            ],
            $entryPointMap1
        );
        $this->assertSame($entryPointMap1, $entryPointMap2);
    }

    /**
     * @test
     */
    public function getDefinitionHints(): void
    {
        $config = new StubCompilerConfig(
            [
                new StubContainerConfig(
                    [],
                    [
                        "X\\A" => DefinitionHint::singleton("X\\A"),
                    ]
                ),
                new StubContainerConfig(
                    [],
                    [
                        "X\\B" => DefinitionHint::singleton("X\\B"),
                    ]
                ),
                new StubContainerConfig(
                    [],
                    [
                        "X\\A" => DefinitionHint::prototype("X\\A"),
                    ]
                ),
            ]
        );

        $definitionHints = $config->getDefinitionHints();

        $this->assertEquals(
            [
                "X\\A" => DefinitionHint::prototype("X\\A"),
                "X\\B" => DefinitionHint::singleton("X\\B"),
            ],
            $definitionHints
        );
    }
}
