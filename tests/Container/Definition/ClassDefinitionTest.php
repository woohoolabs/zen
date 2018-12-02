<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;

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
            $definition->getHash("")
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
        $definition = new ClassDefinition("X\\A");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionSingleton.php"),
            $definition->toPhpCode([$definition->getId("") => $definition])
        );
    }

    /**
     * @test
     */
    public function prototypeWithRequiredConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addConstructorArgumentFromClass("X\\C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithRequiredConstructorDependencies.php"),
            $definition->toPhpCode(
                [
                    $definition->getId("") => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B"),
                    "X\\C" => ClassDefinition::singleton("X\\C"),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function contextDependentConstructorInjectionToPhpCode()
    {
        $definition = ClassDefinition::singleton("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addConstructorArgumentFromClass("X\\C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithContextDependentConstructorDependencies.php"),
            $definition->toPhpCode(
                [
                    $definition->getId("") => $definition,
                    "X\\B" => new ContextDependentDefinition(
                        "X\\B",
                        null,
                        [
                            "X\\A" => new ClassDefinition("X\\C", "singleton"),
                            "X\\F" => new ClassDefinition("X\\D", "singleton"),
                        ]
                    ),
                    "X\\C" => new ClassDefinition("X\\C", "singleton"),
                    "X\\D" => new ClassDefinition("X\\D", "singleton"),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function prototypeWithOptionalConstructorDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addConstructorArgumentFromValue("")
            ->addConstructorArgumentFromValue(true)
            ->addConstructorArgumentFromValue(0)
            ->addConstructorArgumentFromValue(1)
            ->addConstructorArgumentFromValue(1345.999)
            ->addConstructorArgumentFromValue(null)
            ->addConstructorArgumentFromValue(["a" => false]);

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithOptionalConstructorDependencies.php"),
            $definition->toPhpCode(
                [
                    $definition->getId("") => $definition,
                ]
            )
        );
    }

    /**
     * @test
     */
    public function prototypeWithPropertyDependenciesToPhpCode()
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addPropertyFromClass("b", "X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithPropertyDependencies.php"),
            $definition->toPhpCode(
                [
                    $definition->getId("") => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B"),
                    "X\\C" => ClassDefinition::singleton("X\\C"),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function contextDependentPropertyInjectionToPhpCode()
    {
        $definition = ClassDefinition::singleton("X\\A")
            ->addPropertyFromClass("b", "X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithContextDependentPropertyDependencies.php"),
            $definition->toPhpCode(
                [
                    $definition->getId("") => $definition,
                    "X\\B" => new ContextDependentDefinition(
                        "X\\B",
                        null,
                        [
                            "X\\A" => new ClassDefinition("X\\C", "singleton"),
                            "X\\F" => new ClassDefinition("X\\D", "singleton"),
                        ]
                    ),
                    "X\\C" => new ClassDefinition("X\\C", "singleton"),
                    "X\\D" => new ClassDefinition("X\\D", "singleton"),
                ]
            )
        );
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
