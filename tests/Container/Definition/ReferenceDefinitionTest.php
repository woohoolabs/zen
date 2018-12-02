<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;

class ReferenceDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function singletonToPhpCode()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B");

        $phpCode = $definition->toPhpCode(
            [
                $definition->getId("") => $definition,
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
                $definition->getId("") => $definition,
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
