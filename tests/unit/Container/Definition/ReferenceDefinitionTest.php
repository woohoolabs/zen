<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;

class ReferenceDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function singletonToPhpCode()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ReferenceDefinitionSingleton.php"),
            $definition->toPhpCode()
        );
    }

    /**
     * @test
     */
    public function prototypeToPhpCode()
    {
        $definition = ReferenceDefinition::prototype("X\\A", "X\\B");

        $this->assertEquals(
            $this->getDefinitionSourceCode("ReferenceDefinitionPrototype.php"),
            $definition->toPhpCode()
        );
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
