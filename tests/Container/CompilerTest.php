<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition;
use WoohooLabs\Zen\Tests\Double\StubSingletonDefinition;
use function dirname;
use function file_get_contents;

class CompilerTest extends TestCase
{
    /**
     * @test
     */
    public function compileContainerWithoutNamespace()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "", "EmptyContainerWithoutNamespace"),
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithoutNamespace.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithNamespace()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "EmptyContainerWithNamespace"),
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithNamespace.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithoutEntryPointWithEntry()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "ContainerWithEntry"),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(),
            ]
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntry.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithEntryPoint()
    {
        $compiler = new Compiler();

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
            ]
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntryPoint.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithAlwaysAutoloadedClasses()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig(
                [
                    new StubContainerConfig(),
                ],
                "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                "ContainerWithAlwaysAutoloadedClasses",
                true,
                true,
                true,
                [
                    StubSingletonDefinition::class,
                ]
            ),
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithAlwaysAutoloadedClasses.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithUnoptimizedAutoloadedPrototypeEntryPoint()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig(
                [
                    new StubContainerConfig(
                        [
                            StubPrototypeDefinition::class,
                        ]
                    ),
                ],
                "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                "ContainerWithUnoptimizedAutoloadedPrototypeEntryPoint",
                true,
                true,
                true
            ),
            [
                StubPrototypeDefinition::class => new StubPrototypeDefinition(true, true),
            ]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithUnoptimizedAutoloadedPrototypeEntryPoint.php"),
            $container["container"]
        );
    }

    /**
     * @test
     */
    public function compileContainerWithUnoptimizedAutoloadedSingletonEntryPoint()
    {
        $compiler = new Compiler();

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
                "ContainerWithUnoptimizedAutoloadedSingletonEntryPoint",
                true,
                true,
                true
            ),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(true, true, false, 1),
            ]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithUnoptimizedAutoloadedSingletonEntryPoint.php"),
            $container["container"]
        );
    }

    /**
     * @test
     */
    public function compileContainerWithOptimizedAutoloadedEntryPoint()
    {
        $compiler = new Compiler();

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
                "ContainerWithOptimizedAutoloadedEntryPoint",
                true,
                true,
                true
            ),
            [
                StubSingletonDefinition::class => new StubSingletonDefinition(true, true),
            ]
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithOptimizedAutoloadedEntryPoint.php"), $container["container"]);
    }

    /**
     * @test
     */
    public function compileContainerWithFileBasedEntryPoint()
    {
        $compiler = new Compiler();

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
                StubSingletonDefinition::class => new StubSingletonDefinition(true, false, true),
            ]
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

    /**
     * @test
     */
    public function compileContainerWithFileBasedAutoloadedEntryPoint()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig(
                [
                    new StubContainerConfig(
                        [
                            StubPrototypeDefinition::class,
                        ]
                    ),
                ],
                "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                "ContainerWithFileBasedAutoloadedEntryPoint",
                true,
                true,
                true
            ),
            [
                StubPrototypeDefinition::class => new StubPrototypeDefinition(true, true, true),
            ]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithFileBasedAutoloadedEntryPoint.php"),
            $container["container"]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithFileBasedAutoloadedEntryPoint-ProxyDefinition.php"),
            $container["definitions"]["_proxy__WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition.php"]
        );

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithFileBasedAutoloadedEntryPoint-Definition.php"),
            $container["definitions"]["WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition.php"]
        );
    }

    private function getCompiledContainerSourceCode(string $fileName): string
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
