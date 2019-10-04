<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\AutoloadedDefinition;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedE;

use function dirname;
use function file_get_contents;
use function str_replace;

class AutoloadedDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function isSingleton(): void
    {
        $definition = new AutoloadedDefinition("", true);

        $singleton = $definition->isSingleton("");

        $this->assertTrue($singleton);
    }

    /**
     * @test
     */
    public function isAutoloaded(): void
    {
        $definition = new AutoloadedDefinition("");

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function needsDependencyResolution(): void
    {
        $definition = new AutoloadedDefinition("");

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies(): void
    {
        $definition = new AutoloadedDefinition("");

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }

    /**
     * @test
     */
    public function getClassDependencies(): void
    {
        $definition = new AutoloadedDefinition("");

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }

    /**
     * @test
     */
    public function compile(): void
    {
        $definition = new AutoloadedDefinition(MixedE::class);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                new AutoloadConfig(true, dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Mixed/"),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    MixedE::class => ClassDefinition::singleton(MixedE::class, true)
                        ->addConstructorArgumentFromClass(MixedD::class),
                    MixedD::class => new ClassDefinition(MixedD::class),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("AutoloadedDefinition.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function instantiate(): void
    {
        $definition = new AutoloadedDefinition(MixedE::class);

        $this->expectException(ContainerException::class);

        $definition->instantiate($this->createDefinitionInstantiation(), "");
    }

    /**
     * @test
     */
    public function compileWhenIndented(): void
    {
        $definition = new AutoloadedDefinition(MixedE::class);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                new AutoloadConfig(true, dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Mixed/"),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    MixedE::class => ClassDefinition::singleton(MixedE::class, true)
                        ->addConstructorArgumentFromClass(MixedD::class),
                    MixedD::class => new ClassDefinition(MixedD::class),
                ]
            ),
            "",
            2,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("AutoloadedDefinitionWhenIndented.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenFileBased(): void
    {
        $definition = new AutoloadedDefinition(MixedE::class, true, true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                new AutoloadConfig(true, dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Mixed/"),
                FileBasedDefinitionConfig::disabledGlobally("Definitions/"),
                [
                    MixedE::class => ClassDefinition::singleton(MixedE::class, true, true, true)
                        ->addConstructorArgumentFromClass(MixedD::class),
                    MixedD::class => ClassDefinition::singleton(MixedD::class),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("AutoloadedDefinitionWhenFileBased.php"), $compiledDefinition);
    }

    private function createDefinitionInstantiation(): DefinitionInstantiation
    {
        return new DefinitionInstantiation(new RuntimeContainer(new DummyCompilerConfig()));
    }

    private function getDefinitionSourceCode(string $fileName): string
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
