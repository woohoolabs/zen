<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorE;
use function dirname;
use function file_get_contents;
use function str_replace;
use function substr;

class ReferenceDefinitionTest extends TestCase
{
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
    public function instantiateWhenSingleton()
    {
        $definition = ReferenceDefinition::singleton("X\\D", ConstructorD::class, true);
        $instantiation = $this->createDefinitionInstantiation(
            [
                "X\\A" => $definition,
                ConstructorD::class => ClassDefinition::singleton(ConstructorD::class, true),
            ]
        );

        $object1 = $definition->instantiate($instantiation, "");
        $object2 = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorD::class, $object1);
        $this->assertSame($object1, $object2);
    }

    /**
     * @test
     */
    public function instantiateWhenPrototypeWithSingletonReference()
    {
        $definition = ReferenceDefinition::prototype("X\\D", ConstructorD::class, true);
        $instantiation = $this->createDefinitionInstantiation(
            [
                "X\\D" => $definition,
                ConstructorD::class => ClassDefinition::singleton(ConstructorD::class, true),
            ]
        );

        $object1 = $definition->instantiate($instantiation, "");
        $object2 = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorD::class, $object1);
        $this->assertSame($object1, $object2);
    }

    /**
     * @test
     */
    public function instantiateWhenPrototypeWithPrototypeReference()
    {
        $definition = ReferenceDefinition::prototype("X\\D", ConstructorD::class, true);
        $instantiation = $this->createDefinitionInstantiation(
            [
                "X\\D" => $definition,
                ConstructorD::class => ClassDefinition::prototype(ConstructorD::class, true),
            ]
        );

        $object1 = $definition->instantiate($instantiation, "");
        $object2 = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorD::class, $object1);
        $this->assertNotSame($object1, $object2);
    }

    /**
     * @test
     */
    public function compileWhenUnoptimizedSingletonClass()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", false, false, false, 2);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B"),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenUnoptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenUnoptimizedSingletonEntryPoint()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true, false, false, 0, 0);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B"),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenUnoptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenOptimizedSingleton()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", false, false, false, 0, 0);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", false, false, false, [], [], 1, 0),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenOptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenUnoptimizedSingletonReference()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenUnoptimizedSingletonReference.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenPrototype()
    {
        $definition = ReferenceDefinition::prototype("X\\A", "X\\B", true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::prototype("X\\B", true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenPrototype.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenAutoloaded()
    {
        $definition = ReferenceDefinition::singleton(ConstructorE::class, ConstructorD::class, true, true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(dirname(__DIR__, 2)),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    ConstructorE::class => $definition,
                    ConstructorD::class => ClassDefinition::singleton(ConstructorD::class, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenAutoloaded.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenIndented()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true),
                ]
            ),
            "",
            2,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenIndented.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenInlined()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true, false, false);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true),
                ]
            ),
            "",
            0,
            true
        );

        $this->assertEquals($this->getInlinedDefinitionSourceCode("ReferenceDefinitionWhenInlined.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenBothFileBased()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true, false, true);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true, false, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenBothFileBased.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenOnlyChildFileBased()
    {
        $definition = ReferenceDefinition::singleton("X\\A", "X\\B", true, false, false);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true, false, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ReferenceDefinitionWhenOnlyChildFileBased.php"), $compiledDefinition);
    }

    private function createDefinitionInstantiation(array $definitions): DefinitionInstantiation
    {
        $singletonEntries = [];

        return new DefinitionInstantiation(
            new RuntimeContainer(new DummyCompilerConfig()),
            $definitions,
            $singletonEntries
        );
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
