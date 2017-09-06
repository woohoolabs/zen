<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Tests\Unit\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Unit\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Unit\Double\StubDefinition;

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
                new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container", "EmptyContainerWithNamespace"),
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
                new StubCompilerConfig([], "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container", "ContainerWithEntry"),
                [
                    StubDefinition::class => new StubDefinition()
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
                                StubDefinition::class
                            ]
                        ),
                    ],
                    "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container",
                    "ContainerWithEntryPoint"
                ),
                [
                    StubDefinition::class => new StubDefinition()
                ]
            )
        );
    }

    private function getCompiledContainerSourceCode(string $fileName)
    {
        return file_get_contents(dirname(__DIR__) . "/Fixture/Container/" . $fileName);
    }
}
