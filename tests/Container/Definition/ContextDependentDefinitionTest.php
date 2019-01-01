<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Exception\ContainerException;
use function dirname;
use function file_get_contents;
use function str_replace;

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
    public function isSingleton()
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

        $scope = $definition->isSingleton("X\\D");

        $this->assertFalse($scope);
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
    public function compileWithoutDefault()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => ClassDefinition::singleton("X\\C"),
                "X\\D" => ClassDefinition::singleton("X\\E"),
                "X\\F" => ClassDefinition::singleton("X\\G"),
            ]
        );

        $this->expectException(ContainerException::class);

        $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C"),
                    "X\\D" => ClassDefinition::singleton("X\\E"),
                    "X\\F" => ClassDefinition::singleton("X\\G"),
                ]
            ),
            0,
            false
        );
    }

    /**
     * @test
     */
    public function compileWithDefault()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            ClassDefinition::singleton("X\\H", true),
            [
                "X\\B" => ClassDefinition::singleton("X\\C"),
                "X\\D" => ClassDefinition::singleton("X\\E"),
                "X\\F" => ClassDefinition::singleton("X\\G"),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C"),
                    "X\\D" => ClassDefinition::singleton("X\\E"),
                    "X\\F" => ClassDefinition::singleton("X\\G"),
                ]
            ),
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWithDefault.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenIndented()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            ClassDefinition::singleton("X\\H", true),
            [
                "X\\B" => ClassDefinition::singleton("X\\C"),
                "X\\D" => ClassDefinition::singleton("X\\E"),
                "X\\F" => ClassDefinition::singleton("X\\G"),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => new ClassDefinition("X\\C"),
                    "X\\D" => new ClassDefinition("X\\E"),
                    "X\\F" => new ClassDefinition("X\\G"),
                ]
            ),
            2,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWhenIndented.php"), $compiledDefinition);
    }

    private function getDefinitionSourceCode(string $fileName): string
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
