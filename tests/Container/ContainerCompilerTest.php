<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\ContainerCompiler;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Double\StubSingletonDefinition;

use function dirname;
use function file_get_contents;

class ContainerCompilerTest extends TestCase
{
    /**
     * @test
     */
    public function compileContainerWithoutNamespace(): void
    {
        $compiler = new ContainerCompiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "", "EmptyContainerWithoutNamespace"),
            [],
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithoutNamespace.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithNamespace(): void
    {
        $compiler = new ContainerCompiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "EmptyContainerWithNamespace"),
            [],
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithNamespace.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithoutEntryPointWithEntry(): void
    {
        $compiler = new ContainerCompiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "ContainerWithEntry"),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(),
            ],
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntry.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithEntryPoint(): void
    {
        $compiler = new ContainerCompiler();

        $container = $compiler->compile(
            new StubCompilerConfig(
                [
                    new StubContainerConfig(
                        [
                            StubSingletonDefinition::class,
                        ]
                    ),
                ],
                "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                "ContainerWithEntryPoint"
            ),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(true),
            ],
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntryPoint.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithFileBasedEntryPoint(): void
    {
        $compiler = new ContainerCompiler();

        $container = $compiler->compile(
            new StubCompilerConfig(
                [
                    new StubContainerConfig(
                        [
                            StubSingletonDefinition::class,
                        ]
                    ),
                ],
                "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                "ContainerWithFileBasedEntryPoint",
                true,
                true,
                true
            ),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(true, true),
            ],
            []
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithFileBasedEntryPoint.php"),
            $container["container"]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithFileBasedEntryPoint-Definition.php"),
            $container["definitions"]["WoohooLabs__Zen__Tests__Double__StubSingletonDefinition.php"]
        );
    }

    private function getCompiledContainerSourceCode(string $fileName): string
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
