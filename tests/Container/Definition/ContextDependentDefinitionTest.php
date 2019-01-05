<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
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
    public function getId()
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
    public function idIsMissing()
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
    public function getHash()
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
    public function isSingleton()
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
    public function isSingletonWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isSingleton();
    }

    /**
     * @test
     */
    public function isSingletonWithDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A"), []);

        $isSingleton = $definition->isSingleton();

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function isSingletonWithDefaultWhenParentNotExists()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A"), []);

        $isSingleton = $definition->isSingleton("X\\B");

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function isSingletonWhenParentExists()
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
    public function isEntryPointWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isEntryPoint();
    }

    /**
     * @test
     */
    public function isEntryPointWithDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", true), []);

        $isEntryPoint = $definition->isEntryPoint();

        $this->assertTrue($isEntryPoint);
    }

    /**
     * @test
     */
    public function isEntryPointWithDefaultWhenParentNotExists()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", true), []);

        $isEntryPoint = $definition->isEntryPoint("X\\B");

        $this->assertTrue($isEntryPoint);
    }

    /**
     * @test
     */
    public function isEntryPointWhenParentExists()
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
    public function isAutoloadedWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isAutoloaded();
    }

    /**
     * @test
     */
    public function isAutoloadedWithDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, true), []);

        $isAutoloaded = $definition->isAutoloaded();

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function isAutoloadedWithDefaultWhenParentNotExists()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, true), []);

        $isAutoloaded = $definition->isAutoloaded("X\\B");

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function isAutoloadedWhenParentExists()
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B", false, true),
            ]
        );

        $isAutoloaded = $definition->isAutoloaded("X\\A");

        $this->assertTrue($isAutoloaded);
    }

    /**
     * @test
     */
    public function isFileBasedWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->isFileBased();
    }

    /**
     * @test
     */
    public function isFileBasedWithDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, false, true), []);

        $isFileBased = $definition->isFileBased();

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWithDefaultWhenParentNotExists()
    {
        $definition = new ContextDependentDefinition("", ClassDefinition::singleton("X\\A", false, false, true), []);

        $isFileBased = $definition->isFileBased("X\\B");

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function isFileBasedWhenParentExists()
    {
        $definition = new ContextDependentDefinition(
            "",
            null,
            [
                "X\\A" => ClassDefinition::singleton("X\\B", false, false, true),
            ]
        );

        $isFileBased = $definition->isFileBased("X\\A");

        $this->assertTrue($isFileBased);
    }

    /**
     * @test
     */
    public function increaseReferenceCountWithoutDefaultWhenParentNotExists()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->increaseReferenceCount("", true);
    }

    /**
     * @test
     */
    public function increaseReferenceCountWithDefaultWhenNoParent()
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
    public function increaseReferenceCountWithDefaultWhenParentNotExists()
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
    public function increaseReferenceCountWhenParentExists()
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
    public function needsDependencyResolution()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $needsDependencyResolution = $definition->needsDependencyResolution();

        $this->assertFalse($needsDependencyResolution);
    }

    /**
     * @test
     */
    public function resolveDependencies()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $result = $definition->resolveDependencies();

        $this->assertSame($definition, $result);
    }

    /**
     * @test
     */
    public function getClassDependencies()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $classDependencies = $definition->getClassDependencies();

        $this->assertEmpty($classDependencies);
    }

    /**
     * @test
     */
    public function instantiateWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->instantiate($this->createDefinitionInstantiation([]), "");
    }

    /**
     * @test
     */
    public function instantiateWhenDefault()
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
    public function instantiateWhenNotDefault()
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
    public function compileWithoutDefaultWhenNoParent()
    {
        $definition = new ContextDependentDefinition("", null, []);

        $this->expectException(ContainerException::class);

        $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
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
    public function compileWithDefaultWhenParentNotExists()
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
                AutoloadConfig::disabledGlobally(),
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
    public function compileWhenParentExists()
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
                AutoloadConfig::disabledGlobally(),
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
    public function compileWhenIndented()
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
                AutoloadConfig::disabledGlobally(),
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
    public function compileWhenInlined()
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
                AutoloadConfig::disabledGlobally(),
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
    public function compileWhenFileBased()
    {
        $definition = new ContextDependentDefinition(
            "X\\A",
            null,
            [
                "X\\Z" => ClassDefinition::singleton("X\\C", true, false, true),
            ]
        );

        $compiledDefinition = $definition->compile(
            new DefinitionCompilation(
                AutoloadConfig::disabledGlobally(),
                FileBasedDefinitionConfig::disabledGlobally("Definitions"),
                [
                    "X\\Z" => ClassDefinition::singleton("X\\C", true, false, true),
                ]
            ),
            "X\\Z",
            0,
            false
        );

        $this->assertEquals($this->getDefinitionSourceCode("ContextDependentDefinitionWhenFileBased.php"), $compiledDefinition);
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
