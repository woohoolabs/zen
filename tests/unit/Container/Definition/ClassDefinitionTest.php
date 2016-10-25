<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;

class ClassDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getHash()
    {
        $definition = new ClassDefinition("A\\B");

        $this->assertEquals(
            "A__B",
            $definition->getHash()
        );
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $definition = new ClassDefinition("A\\B");

        $this->assertEquals(
            "A\\B",
            $definition->getClassName()
        );
    }

    /**
     * @test
     */
    public function needsDependencyResolutionByDefault()
    {
        $definition = new ClassDefinition("");

        $this->assertTrue($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new ClassDefinition("");
        $definition->resolveDependencies();

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function singletonClassToPhpCode()
    {
        $definition = new ClassDefinition("A");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionSingleton.php"),
            $definition->toPhpCode()
        );
    }

    /**
     * @test
     */
    public function prototypeWithRequiredConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addRequiredConstructorArgument("B")
            ->addRequiredConstructorArgument("C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithRequiredConstructorDependencies.php"),
            $definition->toPhpCode()
        );
    }

    /**
     * @test
     */
    public function prototypeWithOptionalConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addOptionalConstructorArgument("")
            ->addOptionalConstructorArgument(true)
            ->addOptionalConstructorArgument(0)
            ->addOptionalConstructorArgument(1)
            ->addOptionalConstructorArgument(1345.999)
            ->addOptionalConstructorArgument(null)
            ->addOptionalConstructorArgument(["a" => false]);

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithOptionalConstructorDependencies.php"),
            $definition->toPhpCode()
        );
    }

    /**
     * @test
     */
    public function prototypeWithPropertyDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("A")
            ->addProperty("b", "B")
            ->addProperty("c", "C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithPropertyDependencies.php"),
            $definition->toPhpCode()
        );
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(realpath(__DIR__ . "/../../Fixture/Definition/" . $fileName)));
    }
}
