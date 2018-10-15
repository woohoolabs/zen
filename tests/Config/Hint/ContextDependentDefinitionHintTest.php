<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\ContextDependentDefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ContextDependentDefinition;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\ClassF;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\ContextDependent\InterfaceA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassD;

class ContextDependentDefinitionHintTest extends TestCase
{
    /**
     * @test
     */
    public function toDefinitionsWithOnlyDefault()
    {
        $hint = ContextDependentDefinitionHint::create(ClassA::class);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithOnlyPrototypeDefault()
    {
        $hint = ContextDependentDefinitionHint::create(
            DefinitionHint::prototype(ClassA::class)
        );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class, "prototype"),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class, "prototype"),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithOnlyAutoloadedDefault()
    {
        $hint = ContextDependentDefinitionHint::create(ClassA::class);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class, "singleton", true),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class, "singleton", true),
            ],
            $hint->toDefinitions([], InterfaceA::class, true)
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithoutDefault()
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }

    /**
     * @test
     */
    public function toPrototypeDefinitionsWithoutDefault()
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                DefinitionHint::prototype(ClassA::class),
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class, "prototype"),
                        ClassE::class => new ClassDefinition(ClassA::class, "prototype"),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class, "prototype"),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }

    /**
     * @test
     */
    public function toAutoloadedDefinitionsWithoutDefault()
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                DefinitionHint::singleton(ClassA::class),
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class, "singleton", true),
                        ClassE::class => new ClassDefinition(ClassA::class, "singleton", true),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class, "singleton", true),
            ],
            $hint->toDefinitions([], InterfaceA::class, true)
        );
    }

    /**
     * @test
     */
    public function toMultipleDefinitionsWithoutDefault()
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            )
            ->setClassContext(
                DefinitionHint::prototype(ClassB::class),
                [
                    ClassF::class,
                ]
            );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                        ClassF::class => new ClassDefinition(ClassB::class, "prototype"),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
                ClassB::class => new ClassDefinition(ClassB::class, "prototype"),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithDefault()
    {
        $hint = ContextDependentDefinitionHint::create(DefinitionHint::prototype(ClassB::class))
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassB::class, "prototype"),
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
                ClassB::class => new ClassDefinition(ClassB::class, "prototype"),
            ],
            $hint->toDefinitions([], InterfaceA::class, false)
        );
    }
}
