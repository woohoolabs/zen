<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassB;

class DefinitionHintTest extends TestCase
{
    /**
     * @test
     */
    public function singleton()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $scoppe = $hint->getScope();

        $this->assertEquals("singleton", $scoppe);
    }

    /**
     * @test
     */
    public function prototype()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $scope = $hint->getScope();

        $this->assertEquals("prototype", $scope);
    }

    /**
     * @test
     */
    public function setSingletonScope()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $hint->setSingletonScope();

        $this->assertEquals("singleton", $hint->getScope());
    }

    /**
     * @test
     */
    public function toDefinitionsWhenIdMismatch()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $definitions = $hint->toDefinitions([], ClassB::class, false, false, false);

        $this->assertEquals(
            [
                ClassB::class => new ReferenceDefinition(ClassB::class, ClassA::class),
                ClassA::class => new ClassDefinition(ClassA::class),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenParameterAndPropertySetAndIdMismatch()
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setParameter("param", "value")
            ->setProperty("property", "value");

        $definitions = $hint->toDefinitions([], ClassB::class, false, false, false);

        $this->assertEquals(
            [
                ClassB::class => new ReferenceDefinition(ClassB::class, ClassA::class),
                ClassA::class => ClassDefinition::singleton(
                    ClassA::class,
                    false,
                    false,
                    false,
                    [
                        "param" => "value",
                    ],
                    [
                        "property" => "value",
                    ]
                ),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenPrototype()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $definitions = $hint->toDefinitions([], ClassA::class, false, false, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(ClassA::class, "prototype"),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenAutoloaded()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $definitions = $hint->toDefinitions([], ClassA::class, false, true, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(ClassA::class, "singleton", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenParameterSet()
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setParameter("param", ["abc"]);

        $definitions = $hint->toDefinitions([], ClassA::class, false, false, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(
                    ClassA::class,
                    "singleton",
                    false,
                    false,
                    false,
                    [
                        "param" => ["abc"],
                    ],
                    []
                ),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenPropertySet()
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setProperty("property", "value");

        $definitions = $hint->toDefinitions([], ClassA::class, false, false, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(
                    ClassA::class,
                    "singleton",
                    false,
                    false,
                    false,
                    [],
                    [
                        "property" => "value",
                    ]
                ),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function setPrototypeScope()
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $hint->setPrototypeScope();

        $this->assertEquals("prototype", $hint->getScope());
    }

    /**
     * @test
     */
    public function getClassName()
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $className = $hint->getClassName();

        $this->assertEquals(ClassA::class, $className);
    }
}
