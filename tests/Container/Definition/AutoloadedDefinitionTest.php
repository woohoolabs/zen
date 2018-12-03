<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Container\Definition\AutoloadedDefinition;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedE;

class AutoloadedDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getScope()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $scope = $definition->getScope("");

        $this->assertEquals("", $scope);
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function needsDependencyResolution()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new AutoloadedDefinition(new AutoloadConfig(true), "");

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }

    /**
     * @test
     */
    public function toPhpCode()
    {
        $definition = new AutoloadedDefinition(
            new AutoloadConfig(true, dirname(__DIR__) . "/Fixture/DependencyGraph/Mixed"),
            MixedE::class
        );

        $phpCode = $definition->toPhpCode(
            [
                MixedE::class => $definition,
            ]
        );

        $this->assertEquals($this->getDefinitionSourceCode("AutoloadedDefinition.php"), $phpCode);
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
