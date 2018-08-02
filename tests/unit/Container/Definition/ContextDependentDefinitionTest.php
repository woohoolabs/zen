<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Exception\ContainerException;

class ContextDependentDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getId()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals(
            "X\\E",
            $definition->getId("X\\D")
        );
    }

    /**
     * @test
     */
    public function idIsMissing()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
            ]
        );

        $this->expectException(ContainerException::class);

        $definition->getId("X\\D");
    }

    /**
     * @test
     */
    public function getHash()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals(
            "X__E",
            $definition->getHash("X\\D")
        );
    }

    /**
     * @test
     */
    public function getScope()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => ClassDefinition::prototype("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals(
            "prototype",
            $definition->getScope("X\\D")
        );
    }



    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->assertFalse($definition->isAutoloaded());
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->assertEmpty($definition->getClassDependencies());
    }

    /**
     * @test
     */
    public function toPhpCodeWithoutDefault()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ContextDependentDefinitionWithoutDefault.php"),
            $definition->toPhpCode(
                [
                    "X\\B" => new ClassDefinition("X\\C"),
                    "X\\D" => new ClassDefinition("X\\E"),
                    "X\\F" => new ClassDefinition("X\\G"),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function toPhpCodeWithDefault()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            new ClassDefinition("X\\H"),
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ContextDependentDefinitionWithDefault.php"),
            $definition->toPhpCode(
                [
                    "X\\B" => new ClassDefinition("X\\C"),
                    "X\\D" => new ClassDefinition("X\\E"),
                    "X\\F" => new ClassDefinition("X\\G"),
                ]
            )
        );
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
