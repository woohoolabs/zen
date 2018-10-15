<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Double\StubDefinition;

class CompilerTest extends TestCase
{
    /**
     * @test
     */
    public function compileContainerWithoutNamespace()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("EmptyContainerWithoutNamespace.php"),
            $compiler->compile(
                new StubCompilerConfig([], "", "EmptyContainerWithoutNamespace"),
                []
            )
        );
    }

    /**
     * @test
     */
    public function compileContainerWithNamespace()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("EmptyContainerWithNamespace.php"),
            $compiler->compile(
                new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "EmptyContainerWithNamespace"),
                []
            )
        );
    }

    /**
     * @test
     */
    public function compileContainerWithEntry()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithEntry.php"),
            $compiler->compile(
                new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "ContainerWithEntry"),
                [
                    StubDefinition::class => new StubDefinition(),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function compileContainerWithEntryPoint()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithEntryPoint.php"),
            $compiler->compile(
                new StubCompilerConfig(
                    [
                        new StubContainerConfig(
                            [
                                StubDefinition::class,
                            ]
                        ),
                    ],
                    "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                    "ContainerWithEntryPoint"
                ),
                [
                    StubDefinition::class => new StubDefinition(),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function compileContainerWithAlwaysAutoloadedClasses()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithAlwaysAutoloadedClasses.php"),
            $compiler->compile(
                new StubCompilerConfig(
                    [
                        new StubContainerConfig(),
                    ],
                    "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                    "ContainerWithAlwaysAutoloadedClasses",
                    true,
                    true,
                    true,
                    [StubDefinition::class]
                ),
                []
            )
        );
    }

    /**
     * @test
     */
    public function compileContainerWithAutoloadedEntryPoint()
    {
        $compiler = new Compiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("ContainerWithAutoloadedEntryPoint.php"),
            $compiler->compile(
                new StubCompilerConfig(
                    [
                        new StubContainerConfig(
                            [
                                StubDefinition::class,
                            ]
                        ),
                    ],
                    "WoohooLabs\\Zen\\Tests\\Fixture\\Container",
                    "ContainerWithAutoloadedEntryPoint",
                    true,
                    true,
                    true
                ),
                [
                    StubDefinition::class => new StubDefinition(true),
                ]
            )
        );
    }

    private function getCompiledContainerSourceCode(string $fileName): string
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
