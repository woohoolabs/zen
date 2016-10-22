<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Compiler\ArrayMapCompiler;
use WoohooLabs\Zen\Tests\Unit\Fixture\Definition\TestContainerConfigConstructor;
use WoohooLabs\Zen\Tests\Unit\Fixture\Definition\TestContainerConfigEmpty;
use WoohooLabs\Zen\Tests\Unit\Fixture\Definition\TestContainerConfigMixed;

class CompilerTest extends TestCase
{
    /**
     * @test
     */
    public function compileDefinitionsWithoutDefinitions()
    {
        $compiler = new ArrayMapCompiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("TestContainerEmpty.php"),
            $compiler->compileDefinitions(
                "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container",
                "TestContainerEmpty",
                []
            )
        );
    }

    /**
     * @test
     */
    public function compileDefinitionsWithEmptyEntrypoints()
    {
        $compiler = new ArrayMapCompiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("TestContainerEmpty.php"),
            $compiler->compileDefinitions(
                "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container",
                "TestContainerEmpty",
                [
                    new TestContainerConfigEmpty()
                ]
            )
        );
    }

    /**
     * @test
     */
    public function compileDefinitionsWithConstructorInjection()
    {
        $compiler = new ArrayMapCompiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("TestContainerConstructor.php"),
            $compiler->compileDefinitions(
                "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container",
                "TestContainerConstructor",
                [
                    new TestContainerConfigConstructor()
                ]
            )
        );
    }

    /**
     * @test
     */
    public function compileDefinitions()
    {
        $compiler = new ArrayMapCompiler();

        $this->assertEquals(
            $this->getCompiledContainerSourceCode("TestContainerMixed.php"),
            $compiler->compileDefinitions(
                "WoohooLabs\\Zen\\Tests\\Unit\\Fixture\\Container",
                "TestContainerMixed",
                [
                    new TestContainerConfigMixed()
                ]
            )
        );
    }

    private function getCompiledContainerSourceCode(string $fileName)
    {
        return file_get_contents(realpath(__DIR__ . "/../Fixture/Container/" . $fileName));
    }
}
