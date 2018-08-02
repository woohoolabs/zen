<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;

class SelfDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new SelfDefinition("");

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new SelfDefinition("");

        $this->assertEmpty($definition->getClassDependencies());
    }

    /**
     * @test
     */
    public function ToPhpCode()
    {
        $definition = new SelfDefinition("");

        $this->assertEquals($this->getDefinitionSourceCode("SelfDefinition.php"), $definition->toPhpCode([]));
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
