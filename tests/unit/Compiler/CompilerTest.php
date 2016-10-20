<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Compiler\ArrayMapCompiler;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Definition\TestDefinitionConstructor;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Definition\TestDefinitionEmpty;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Definition\TestDefinitionMixed;

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
                "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\Container",
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
                "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\Container",
                "TestContainerEmpty",
                [
                    new TestDefinitionEmpty()
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
                "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\Container",
                "TestContainerConstructor",
                [
                    new TestDefinitionConstructor()
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
                "WoohooLabs\\Dicone\\Tests\\Unit\\Fixture\\Container",
                "TestContainerMixed",
                [
                    new TestDefinitionMixed()
                ]
            )
        );
    }

    private function getCompiledContainerSourceCode(string $fileName)
    {
        return file_get_contents(realpath(__DIR__ . "/../Fixture/Container/" . $fileName));
    }
}
