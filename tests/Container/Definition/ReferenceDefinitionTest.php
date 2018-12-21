<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use function dirname;
use function file_get_contents;
use function str_replace;

class ReferenceDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new ReferenceDefinition("", "");

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertFalse($isAutoloaded);
    }

    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new ReferenceDefinition("", "");

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new ReferenceDefinition("", "");

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new ReferenceDefinition("X\\A", "X\\B");

        $classDependencies = $definition->getClassDependencies();

        $this->assertEquals(["X\\B"], $classDependencies);
    }

    /**
     * @test
     */
    public function singletonToPhpCode()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B");

        $phpCode = $definition->toPhpCode(
            [
                "X\\A" => $definition,
                "X\\B" => ClassDefinition::singleton("X\\B"),
            ]
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionSingleton.php"), $phpCode);
    }

    /**
     * @test
     */
    public function prototypeToPhpCode()
    {
        $definition = ReferenceDefinition::prototype("X\\A", "X\\B");

        $phpCode = $definition->toPhpCode(
            [
                "X\\A" => $definition,
                "X\\B" => ClassDefinition::prototype("X\\B"),
            ]
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionPrototype.php"), $phpCode);
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
