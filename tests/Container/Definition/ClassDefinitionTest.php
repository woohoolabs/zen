<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionCompilation;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;

use function dirname;
use function file_get_contents;
use function str_replace;

class ClassDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function singleton(): void
    {
        $definition = ClassDefinition::singleton("");

        $isSingleton = $definition->isSingleton("");

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function prototype(): void
    {
        $definition = ClassDefinition::prototype("");

        $isSingleton = $definition->isSingleton("");

        $this->assertFalse($isSingleton);
    }

    /**
     * @test
     */
    public function getClassName(): void
    {
        $definition = new ClassDefinition("A\\B");

        $className = $definition->getClassName();

        $this->assertEquals("A\\B", $className);
    }

    /**
     * @test
     */
    public function needsDependencyResolutionByDefault(): void
    {
        $definition = new ClassDefinition("");

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertTrue($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies(): void
    {
        $definition = new ClassDefinition("");

        $definition->resolveDependencies();

        $this->assertFalse($definition->needsDependencyResolution());
    }

    /**
     * @test
     */
    public function isConstructorParameterOverriddenWhenTrue(): void
    {
        $definition = ClassDefinition::singleton(
            "A\\B",
            false,
            false,
            [
                "param1" => "value",
                "param2" => null,
            ]
        );

        $isConstructorParameterOverridden = $definition->isConstructorParameterOverridden("param2");

        $this->assertTrue($isConstructorParameterOverridden);
    }

    /**
     * @test
     */
    public function isConstructorParameterOverriddenWhenFalse(): void
    {
        $definition = ClassDefinition::singleton(
            "A\\B",
            false,
            false,
            [
                "param1" => "value",
                "param2" => null,
            ]
        );

        $isConstructorParameterOverridden = $definition->isConstructorParameterOverridden("param3");

        $this->assertFalse($isConstructorParameterOverridden);
    }

    /**
     * @test
     */
    public function getOverriddenConstructorParameters(): void
    {
        $definition = ClassDefinition::singleton(
            "A\\B",
            false,
            false,
            [
                "param1" => "value",
                "param2" => null,
            ]
        );

        $overriddenConstructorParameters = $definition->getOverriddenConstructorParameters();

        $this->assertEquals(
            ["param1", "param2"],
            $overriddenConstructorParameters
        );
    }

    /**
     * @test
     */
    public function getClassDependenciesWhenEmpty(): void
    {
        $definition = ClassDefinition::singleton("X\\A");

        $dependencies = $definition->getClassDependencies();

        $this->assertEmpty($dependencies);
    }

    /**
     * @test
     */
    public function getClassDependencies(): void
    {
        $definition = ClassDefinition::singleton("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $dependencies = $definition->getClassDependencies();

        $this->assertEquals(
            [
                "X\\B",
                "X\\C",
            ],
            $dependencies
        );
    }

    /**
     * @test
     */
    public function instantiateWhenSingleton(): void
    {
        $definition = ClassDefinition::singleton(ConstructorD::class, true);
        $instantiation = $this->createDefinitionInstantiation([ConstructorD::class => $definition]);

        $object1 = $definition->instantiate($instantiation, "");
        $object2 = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorD::class, $object1);
        $this->assertSame($object1, $object2);
    }

    /**
     * @test
     */
    public function instantiateWhenPrototype(): void
    {
        $definition = ClassDefinition::prototype(ConstructorD::class, true);
        $instantiation = $this->createDefinitionInstantiation([ConstructorD::class => $definition]);

        $object1 = $definition->instantiate($instantiation, "");
        $object2 = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorD::class, $object1);
        $this->assertNotSame($object1, $object2);
    }

    /**
     * @test
     */
    public function instantiateWithConstructorArguments(): void
    {
        $definition = ClassDefinition::singleton(ConstructorA::class, true)
            ->addConstructorArgumentFromClass(ConstructorB::class)
            ->addConstructorArgumentFromValue(0)
            ->addConstructorArgumentFromValue(false)
            ->addConstructorArgumentFromValue("abc");
        $instantiation = $this->createDefinitionInstantiation(
            [
                ConstructorA::class => $definition,
                ConstructorB::class => ClassDefinition::singleton(ConstructorB::class),
            ]
        );

        $object = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(ConstructorA::class, $object);
    }

    /**
     * @test
     */
    public function instantiateWithProperties(): void
    {
        $definition = ClassDefinition::singleton(AnnotationB::class, true, false, [], ["value" => "abc"])
            ->addPropertyFromClass("d", AnnotationD::class)
            ->addPropertyFromOverride("value");
        $instantiation = $this->createDefinitionInstantiation(
            [
                AnnotationB::class => $definition,
                AnnotationD::class => ClassDefinition::singleton(ConstructorD::class),
            ]
        );

        $object = $definition->instantiate($instantiation, "");

        $this->assertInstanceOf(AnnotationB::class, $object);
    }

    /**
     * @test
     */
    public function compileWhenUnoptimizedSingletonClass(): void
    {
        $definition = ClassDefinition::singleton("X\\A", false, false, [], [], 2);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ClassDefinitionUnoptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenUnoptimizedSingletonEntryPoint(): void
    {
        $definition = ClassDefinition::singleton("X\\A", true, false, [], [], 0);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ClassDefinitionUnoptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenOptimizedSingletonClass(): void
    {
        $definition = ClassDefinition::singleton("X\\A", false, false, [], [], 1);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ClassDefinitionOptimizedSingleton.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenPrototypeWithOptionalConstructorDependencies(): void
    {
        $definition = ClassDefinition::prototype("X\\A");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWhenPrototype.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWithRequiredEntryPointConstructorDependencies(): void
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addConstructorArgumentFromClass("X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true),
                    "X\\C" => ClassDefinition::singleton("X\\C", true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithRequiredEntryPointConstructorDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenPrototypeWithRequiredInlinedConstructorDependencies(): void
    {
        $definition = ClassDefinition::singleton("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addConstructorArgumentFromClass("X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", false, false, [], [], 0, 1),
                    "X\\C" => ClassDefinition::singleton("X\\C", false, false, [], [], 0, 1),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithRequiredInlinedConstructorDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenContextDependentConstructorInjection(): void
    {
        $definition = ClassDefinition::singleton("X\\A", true)
            ->addConstructorArgumentFromClass("X\\B")
            ->addConstructorArgumentFromClass("X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => new ContextDependentDefinition(
                        "X\\B",
                        null,
                        [
                            "X\\A" => ClassDefinition::singleton("X\\C", true),
                            "X\\F" => ClassDefinition::singleton("X\\D", true),
                        ]
                    ),
                    "X\\C" => ClassDefinition::singleton("X\\C", true),
                    "X\\D" => ClassDefinition::singleton("X\\D", true),
                ]
            ),
            $definition->getId(""),
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithContextDependentEntryPointConstructorDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWithOptionalConstructorDependencies(): void
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addConstructorArgumentFromValue("")
            ->addConstructorArgumentFromValue(true)
            ->addConstructorArgumentFromValue(0)
            ->addConstructorArgumentFromValue(1)
            ->addConstructorArgumentFromValue(1345.999)
            ->addConstructorArgumentFromValue(null)
            ->addConstructorArgumentFromValue(["a" => false]);

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithOptionalConstructorDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWithOverriddenConstructorDependencies(): void
    {
        $definition = ClassDefinition::prototype(
            "X\\A",
            false,
            false,
            [
                "param1" => "",
                "param2" => null,
                "param3" => 0,
                "param4" => ["a" => false],
            ]
        )
            ->addConstructorArgumentFromOverride("param1")
            ->addConstructorArgumentFromOverride("param2")
            ->addConstructorArgumentFromOverride("param3")
            ->addConstructorArgumentFromOverride("param4");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithOverriddenConstructorDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWithPropertyDependencies(): void
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addPropertyFromClass("b", "X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", false, false, [], [], 0, 1),
                    "X\\C" => ClassDefinition::singleton("X\\C", false, false, [], [], 0, 1),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithPropertyDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWithOverriddenPropertyDependencies(): void
    {
        $definition = ClassDefinition::prototype(
            "X\\A",
            false,
            false,
            [],
            [
                "b" => "abc",
                "c" => null,
                "d" => 0,
            ]
        )
            ->addPropertyFromOverride("b")
            ->addPropertyFromOverride("c")
            ->addPropertyFromOverride("d");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithOverriddenPropertyDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenContextDependentPropertyInjection(): void
    {
        $definition = ClassDefinition::singleton("X\\A", true)
            ->addPropertyFromClass("b", "X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => new ContextDependentDefinition(
                        "X\\B",
                        null,
                        [
                            "X\\A" => new ClassDefinition("X\\C", true, true),
                            "X\\F" => new ClassDefinition("X\\D", true, true),
                        ]
                    ),
                    "X\\C" => new ClassDefinition("X\\C", true, true),
                    "X\\D" => new ClassDefinition("X\\D", true, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWithContextDependentPropertyDependencies.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenMultipleReferenceForOptimizableClass(): void
    {
        $definition = ClassDefinition::singleton("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addPropertyFromClass("b", "X\\B");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", false, false, [], [], 2),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWhenMultipleReferenceForOptimizableClass.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenIndented(): void
    {
        $definition = ClassDefinition::prototype("X\\A")
            ->addConstructorArgumentFromClass("X\\B")
            ->addPropertyFromClass("b", "X\\B")
            ->addPropertyFromClass("c", "X\\C");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", false, false, [], [], 0, 2),
                    "X\\C" => ClassDefinition::singleton("X\\C", false, false, [], [], 0, 1)
                        ->addConstructorArgumentFromClass("X\\D")
                        ->addPropertyFromClass("e", "X\\E"),
                    "X\\D" => ClassDefinition::singleton("X\\D", false, false, [], [], 1, 0),
                    "X\\E" => ClassDefinition::singleton("X\\E", false, false, [], [], 1, 0),
                ]
            ),
            "",
            2,
            false
        );

        $this->assertEquals(
            $this->getDefinitionSourceCode("ClassDefinitionWhenIndented.php"),
            $compiledDefinition
        );
    }

    /**
     * @test
     */
    public function compileWhenBothFileBased(): void
    {
        $definition = ClassDefinition::singleton("X\\A", true, true)
            ->addConstructorArgumentFromClass("X\\B");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ClassDefinitionWhenBothFileBased.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenOnlyChildFileBased(): void
    {
        $definition = ClassDefinition::singleton("X\\A", true, false)
            ->addConstructorArgumentFromClass("X\\B");

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\A" => $definition,
                    "X\\B" => ClassDefinition::singleton("X\\B", true, true),
                ]
            ),
            "",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ClassDefinitionWhenOnlyChildFileBased.php"), $compiledDefinition);
    }

    /**
     * @param DefinitionInterface[] $definitions
     */
    private function createDefinitionInstantiation(array $definitions): DefinitionInstantiation
    {
        $instantiation = new DefinitionInstantiation(new RuntimeContainer(new DummyCompilerConfig()));
        $instantiation->definitions = $definitions;

        return $instantiation;
    }

    private function getDefinitionSourceCode(string $fileName): string
    {
        return str_replace("<?php\n", "", file_get_contents(dirname(__DIR__, 2) . "/Fixture/Definition/" . $fileName));
    }
}
