<?php
namespace WoohooLabs\Dicone\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Compiler\Compiler;
use WoohooLabs\Dicone\Compiler\CompilerConfig;
use WoohooLabs\Dicone\Resolver\DependencyResolver;
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
        $compiler = new Compiler(
            new DependencyResolver(
                new CompilerConfig(true, false)
            )
        );

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
        $compiler = new Compiler(
            new DependencyResolver(
                new CompilerConfig(true, false)
            )
        );

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
        $compiler = new Compiler(
            new DependencyResolver(
                new CompilerConfig(true, false)
            )
        );

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
        $compiler = new Compiler(
            new DependencyResolver(
                new CompilerConfig(true, true)
            )
        );

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
