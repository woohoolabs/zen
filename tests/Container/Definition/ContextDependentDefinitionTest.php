<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

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

        $id = $definition->getId("X\\D");

        $this->assertEquals("X\\E", $id);
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

        $hash = $definition->getHash("X\\D");

        $this->assertEquals("X__E", $hash);
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

        $scope = $definition->getScope("X\\D");

        $this->assertEquals("prototype", $scope);
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertFalse($isAutoloaded);
    }

    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }



    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
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

        $phpCode = $definition->toPhpCode(
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWithoutDefault.php"), $phpCode);
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

        $phpCode = $definition->toPhpCode(
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWithDefault.php"), $phpCode);
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
