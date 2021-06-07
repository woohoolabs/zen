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
    public function toDefinitionsWithOnlyDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create(ClassA::class);

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithOnlySetDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setDefaultClass(ClassA::class);

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithOnlyPrototypeDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create(
            DefinitionHint::prototype(ClassA::class)
        );

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassA::class, false),
                    []
                ),
                ClassA::class => new ClassDefinition(ClassA::class, false),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithoutDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

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
            $definitions
        );
    }

    /**
     * @test
     */
    public function toPrototypeDefinitionsWithoutDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create()
            ->setClassContext(
                DefinitionHint::prototype(ClassA::class),
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class, false),
                        ClassE::class => new ClassDefinition(ClassA::class, false),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class, false),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toMultipleDefinitionsWithoutDefault(): void
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

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    null,
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                        ClassF::class => new ClassDefinition(ClassB::class, false),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
                ClassB::class => new ClassDefinition(ClassB::class, false),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create(DefinitionHint::prototype(ClassB::class))
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            );

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassB::class, false),
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
                ClassB::class => new ClassDefinition(ClassB::class, false),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function toDefinitionsWithOverriddenDefault(): void
    {
        $hint = ContextDependentDefinitionHint::create(DefinitionHint::prototype(ClassA::class))
            ->setClassContext(
                ClassA::class,
                [
                    ClassD::class,
                    ClassE::class,
                ]
            )
            ->setDefaultClass(DefinitionHint::prototype(ClassB::class));

        $definitions = $hint->toDefinitions([], [], InterfaceA::class, false, false);

        $this->assertEquals(
            [
                InterfaceA::class => new ContextDependentDefinition(
                    InterfaceA::class,
                    new ClassDefinition(ClassB::class, false),
                    [
                        ClassD::class => new ClassDefinition(ClassA::class),
                        ClassE::class => new ClassDefinition(ClassA::class),
                    ]
                ),
                ClassA::class => new ClassDefinition(ClassA::class),
                ClassB::class => new ClassDefinition(ClassB::class, false),
            ],
            $definitions
        );
    }
}
