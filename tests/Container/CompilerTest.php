<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Double\StubDefinition;
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

        $container = $compiler->compile(new StubCompilerConfig([], "", "EmptyContainerWithoutNamespace"), []);

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithoutNamespace.php"), $container);
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

        $this->assertEquals($this->getCompiledContainerSourceCode("EmptyContainerWithNamespace.php"), $container);
    }

    /**
     * @test
     */
    public function compileContainerWithEntry()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
            new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Fixture\\Container", "ContainerWithEntry"),
            [
                StubDefinition::class => new StubDefinition(),
            ]
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntry.php"), $container);
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
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithEntryPoint.php"), $container);
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
                [StubDefinition::class]
            ),
            []
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithAlwaysAutoloadedClasses.php"), $container);
    }

    /**
     * @test
     */
    public function compileContainerWithAutoloadedEntryPoint()
    {
        $compiler = new Compiler();

        $container = $compiler->compile(
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
        );

        $this->assertEquals($this->getCompiledContainerSourceCode("ContainerWithAutoloadedEntryPoint.php"), $container);
    }

    private function getCompiledContainerSourceCode(string $fileName): string
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
