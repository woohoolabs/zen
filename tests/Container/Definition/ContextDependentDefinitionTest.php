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
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\RuntimeContainer;
use WoohooLabs\Zen\Tests\Double\DummyCompilerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;

use function dirname;
use function file_get_contents;
use function str_replace;
use function substr;

class ContextDependentDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function getId(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $id = $definition->getId("X\\D");

        $this->assertEquals("X\\E", $id);
    }

    /**
     * @test
     */
    public function idIsMissing(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
            ]
        );

        $this->expectException(ContainerException::class);

        $definition->getId("X\\D");
    }

    /**
     * @test
     */
    public function getHash(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => new ClassDefinition("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $hash = $definition->getHash("X\\D");

        $this->assertEquals("X__E", $hash);
    }

    /**
     * @test
     */
    public function isSingleton(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => new ClassDefinition("X\\C"),
                "X\\D" => ClassDefinition::prototype("X\\E"),
                "X\\F" => new ClassDefinition("X\\G"),
            ]
        );

        $scope = $definition->isSingleton("X\\D");

        $this->assertFalse($scope);
    }

    /**
     * @test
     */
    public function isSingletonWithoutDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isSingleton();
    }

    /**
     * @test
     */
    public function isSingletonWithDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A"), []);

        $isSingleton = $definition->isSingleton();

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function isSingletonWithDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A"), []);

        $isSingleton = $definition->isSingleton("X\\B");

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function isSingletonWhenParentExists(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B", true),
            ]
        );

        $isSingleton = $definition->isSingleton("X\\A");

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function isEntryPointWithoutDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isEntryPoint();
    }

    /**
     * @test
     */
    public function isEntryPointWithDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", true), []);

        $isEntryPoint = $definition->isEntryPoint();

        $this->assertTrue($isEntryPoint);
    }

    /**
     * @test
     */
    public function isEntryPointWithDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", true), []);

        $isEntryPoint = $definition->isEntryPoint("X\\B");

        $this->assertTrue($isEntryPoint);
    }

    /**
     * @test
     */
    public function isEntryPointWhenParentExists(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B", true),
            ]
        );

        $isEntryPoint = $definition->isEntryPoint("X\\A");

        $this->assertTrue($isEntryPoint);
    }

    /**
     * @test
     */
    public function isFileBasedWithoutDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isFileBased();
    }

    /**
     * @test
     */
    public function isFileBasedWithDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, true), []);

        $isFileBased = $definition->isFileBased();

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWithDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, true), []);

        $isFileBased = $definition->isFileBased("X\\B");

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWhenParentExists(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B", false, true),
            ]
        );

        $isFileBased = $definition->isFileBased("X\\A");

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function increaseReferenceCountWithoutDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->increaseReferenceCount("", true);
    }

    /**
     * @test
     */
    public function increaseReferenceCountWithDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            ClassDefinition::singleton("X\\A"),
            []
        );

        $definition
            ->increaseReferenceCount("", true)
            ->increaseReferenceCount("", false);

        $this->assertFalse($definition->isSingletonCheckEliminable(""));
    }

    /**
     * @test
     */
    public function increaseReferenceCountWithDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            ClassDefinition::singleton("X\\A"),
            []
        );

        $definition
            ->increaseReferenceCount("X\\B", true)
            ->increaseReferenceCount("X\\B", false);

        $this->assertFalse($definition->isSingletonCheckEliminable(""));
    }

    /**
     * @test
     */
    public function increaseReferenceCountWhenParentExists(): void
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B"),
            ]
        );

        $definition
            ->increaseReferenceCount("X\\A", true)
            ->increaseReferenceCount("X\\A", false);

        $this->assertFalse($definition->isSingletonCheckEliminable("X\\A"));
    }

    /**
     * @test
     */
    public function needsDependencyResolution(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }

    /**
     * @test
     */
    public function getClassDependencies(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }

    /**
     * @test
     */
    public function instantiateWithoutDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->instantiate($this->createDefinitionInstantiation([]), "");
    }

    /**
     * @test
     */
    public function instantiateWhenDefault(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            ClassDefinition::singleton(ConstructorD::class, true),
            []
        );

        $object = $definition->instantiate(
            $this->createDefinitionInstantiation(
                [
                    "X\\A" => $definition,
                ]
            ),
            ""
        );

        $this->assertInstanceOf(ConstructorD::class, $object);
    }

    /**
     * @test
     */
    public function instantiateWhenNotDefault(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "" => ClassDefinition::singleton(ConstructorD::class, true),
            ]
        );

        $object = $definition->instantiate(
            $this->createDefinitionInstantiation(
                [
                    "X\\A" => $definition,
                ]
            ),
            ""
        );

        $this->assertInstanceOf(ConstructorD::class, $object);
    }

    /**
     * @test
     */
    public function compileWithoutDefaultWhenNoParent(): void
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                []
            ),
            "",
            0,
            false
        );
    }

    /**
     * @test
     */
    public function compileWithDefaultWhenParentNotExists(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            ClassDefinition::singleton("X\\H", true),
            [
                "X\\B" => ClassDefinition::singleton("X\\C"),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C"),
                ]
            ),
            "X\\Y",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWithDefault.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenParentExists(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => ClassDefinition::singleton("X\\C", true),
                "X\\D" => ClassDefinition::singleton("X\\E"),
                "X\\F" => ClassDefinition::singleton("X\\G"),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C", true),
                    "X\\D" => ClassDefinition::singleton("X\\E"),
                    "X\\F" => ClassDefinition::singleton("X\\G"),
                ]
            ),
            "X\\B",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWhenParentExists.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenIndented(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => ClassDefinition::singleton("X\\C", true),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C", true),
                ]
            ),
            "X\\B",
            2,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWhenIndented.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenInlined(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\B" => ClassDefinition::singleton("X\\C", true),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally(),
                [
                    "X\\B" => ClassDefinition::singleton("X\\C", true),
                ]
            ),
            "X\\B",
            0,
            true
        );

        $this->assertEquals($this->getInlinedDefinitionSourceCode("ContextDependentDefinitionWhenInlined.php"), $compiledDefinition);
    }

    /**
     * @test
     */
    public function compileWhenFileBased(): void
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\Z" => ClassDefinition::singleton("X\\C", true, true),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\Z" => ClassDefinition::singleton("X\\C", true, true),
                ]
            ),
            "X\\Z",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWhenFileBased.php"), $compiledDefinition);
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

    private function getInlinedDefinitionSourceCode(string $fileName): string
    {
        return substr($this->getDefinitionSourceCode($fileName), 0, -2);
    }
}
