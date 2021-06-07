<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Hint;

use PHPUnit\Framework\TestCase;
use stdClass;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassB;

class DefinitionHintTest extends TestCase
{
    /**
     * @test
     */
    public function singleton(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $isSingleton = $hint->isSingleton();

        $this->assertTrue($isSingleton);
    }

    /**
     * @test
     */
    public function prototype(): void
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $isSingleton = $hint->isSingleton();

        $this->assertFalse($isSingleton);
    }

    /**
     * @test
     */
    public function setSingletonScope(): void
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $hint->setSingletonScope();

        $this->assertTrue($hint->isSingleton());
    }

    /**
     * @test
     */
    public function toDefinitionsWhenIdMismatch(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $definitions = $hint->toDefinitions([], [], ClassB::class, false);

        $this->assertEquals(
            [
                ClassB::class => new ReferenceDefinition(ClassB::class, ClassA::class, true, false, false, 0),
                ClassA::class => new ClassDefinition(ClassA::class, true, false, false, [], [], 1),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function setParameterWhenNotScalarOrArray(): void
    {
        $this->expectException(ContainerException::class);

        DefinitionHint::singleton(ClassA::class)
            ->setParameter("param", new stdClass());
    }

    /**
     * @test
     */
    public function setPropertyWhenNotScalarOrArray(): void
    {
        $this->expectException(ContainerException::class);

        DefinitionHint::singleton(ClassA::class)
            ->setProperty("param", new stdClass());
    }

    /**
     * @test
     */
    public function toDefinitionsWhenParameterAndPropertySetAndIdMismatch(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setParameter("param", "value")
            ->setProperty("property", "value");

        $definitions = $hint->toDefinitions([], [], ClassB::class, false);

        $this->assertEquals(
            [
                ClassB::class => new ReferenceDefinition(ClassB::class, ClassA::class),
                ClassA::class => ClassDefinition::singleton(
                    ClassA::class,
                    false,
                    false,
                    [
                        "param" => "value",
                    ],
                    [
                        "property" => "value",
                    ],
                    1
                ),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenPrototype(): void
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $definitions = $hint->toDefinitions([], [], ClassA::class, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(ClassA::class, false),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWhenParameterSet(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setParameter("param", ["abc"]);

        $definitions = $hint->toDefinitions([], [], ClassA::class, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(
                    ClassA::class,
                    true,
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
    public function toDefinitionsWhenPropertySet(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class)
            ->setProperty("property", "value");

        $definitions = $hint->toDefinitions([], [], ClassA::class, false);

        $this->assertEquals(
            [
                ClassA::class => new ClassDefinition(
                    ClassA::class,
                    true,
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
    public function setPrototypeScope(): void
    {
        $hint = DefinitionHint::singleton(ClassA::class);

        $hint->setPrototypeScope();

        $this->assertFalse($hint->isSingleton());
    }

    /**
     * @test
     */
    public function getClassName(): void
    {
        $hint = DefinitionHint::prototype(ClassA::class);

        $className = $hint->getClassName();

        $this->assertEquals(ClassA::class, $className);
    }
}
