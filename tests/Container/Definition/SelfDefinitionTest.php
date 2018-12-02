<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

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

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new SelfDefinition("");

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }

    /**
     * @test
     */
    public function ToPhpCode()
    {
        $definition = new SelfDefinition("");

        $phpCode = $definition->toPhpCode([]);

        $this->assertEquals($this->getDefinitionSourceCode("SelfDefinition.php"), $phpCode);
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
