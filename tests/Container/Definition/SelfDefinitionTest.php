<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use function dirname;
use function file_get_contents;
use function str_replace;
use function substr;

class SelfDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function isAutoloaded()
    {
        $definition = new SelfDefinition("");

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertFalse($isAutoloaded);
    }

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
    public function resolveDependencies()
    {
        $definition = new SelfDefinition("");

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
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
    public function compile()
    {
        $definition = new SelfDefinition("");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                []
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("SelfDefinition.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenIndented()
    {
        $definition = new SelfDefinition("");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                []
            ),
            "",
            2,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("SelfDefinitionWhenIndented.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenInlined()
    {
        $definition = new SelfDefinition("");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                []
            ),
            "",
            0,
            true
        );

        $this->assertEquals($this->getInlinedDefinitionSourceCode("SelfDefinitionWhenInlined.php"), $compiledDefinition);
    }

    private function getDefinitionSourceCode(string $fileName): string
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }

    private function getInlinedDefinitionSourceCode(string $fileName): string
    {
        return substr($this->getDefinitionSourceCode($fileName), 0, -2);
    }
}
