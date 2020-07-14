<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Container\ContainerDependencyResolver;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Exception\NotFoundException;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Double\StubContainerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Annotation\AnnotationE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception\ExceptionF;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedD;

class ContainerDependencyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveClassWhenNonEntryPoint(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ConstructorA::class);

        $this->expectException(NotFoundException::class);

        $dependencyResolver->resolveEntryPoint(ConstructorB::class);
    }

    /**
     * @test
     */
    public function resolveClassWhenConstructorDependencies(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ConstructorA::class);

        $definitions = $dependencyResolver->resolveEntryPoint(ConstructorA::class);

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                ConstructorA::class => ClassDefinition::singleton(ConstructorA::class, true)
                    ->addConstructorArgumentFromClass(ConstructorB::class)
                    ->addConstructorArgumentFromClass(ConstructorC::class)
                    ->addConstructorArgumentFromValue(true)
                    ->addConstructorArgumentFromValue(null)
                    ->resolveDependencies(),
                ConstructorB::class => ClassDefinition::singleton(ConstructorB::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                ConstructorC::class => ClassDefinition::singleton(ConstructorC::class)
                    ->addConstructorArgumentFromClass(ConstructorD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                ConstructorD::class => ClassDefinition::singleton(ConstructorD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolveConstructorDependencies(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ConstructorA::class);

        $definitions = $dependencyResolver->resolveEntryPoints();

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                ConstructorA::class => ClassDefinition::singleton(ConstructorA::class, true)
                    ->addConstructorArgumentFromClass(ConstructorB::class)
                    ->addConstructorArgumentFromClass(ConstructorC::class)
                    ->addConstructorArgumentFromValue(true)
                    ->addConstructorArgumentFromValue(null)
                    ->resolveDependencies(),
                ConstructorB::class => ClassDefinition::singleton(ConstructorB::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                ConstructorC::class => ClassDefinition::singleton(ConstructorC::class)
                    ->addConstructorArgumentFromClass(ConstructorD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                ConstructorD::class => ClassDefinition::singleton(ConstructorD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolvePropertyDependencies(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            AnnotationA::class,
            [
                AnnotationB::class => DefinitionHint::singleton(AnnotationB::class)
                    ->setProperty("value", "abc"),
            ]
        );

        $definitions = $dependencyResolver->resolveEntryPoints();

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                AnnotationA::class => ClassDefinition::singleton(AnnotationA::class, true)
                    ->addPropertyFromClass("b", AnnotationB::class)
                    ->addPropertyFromClass("c", AnnotationC::class)
                    ->resolveDependencies(),
                AnnotationB::class => ClassDefinition::singleton(AnnotationB::class, false, false, [], ["value" => "abc"])
                    ->addPropertyFromClass("e2", AnnotationE::class)
                    ->addPropertyFromClass("d", AnnotationD::class)
                    ->addPropertyFromOverride("value")
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                AnnotationC::class => ClassDefinition::singleton(AnnotationC::class)
                    ->addPropertyFromClass("e1", AnnotationE::class)
                    ->addPropertyFromClass("e2", AnnotationE::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                AnnotationE::class => ClassDefinition::singleton(AnnotationE::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true)
                    ->increaseReferenceCount("", true)
                    ->increaseReferenceCount("", true),
                AnnotationD::class => ClassDefinition::singleton(AnnotationD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolveAllDependencies(): void
    {
        $dependencyResolver = $this->createDependencyResolver(MixedA::class);

        $definitions = $dependencyResolver->resolveEntryPoints();

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                MixedA::class => ClassDefinition::singleton(MixedA::class, true)
                    ->addConstructorArgumentFromClass(MixedB::class)
                    ->addConstructorArgumentFromClass(MixedC::class)
                    ->addPropertyFromClass("d", MixedD::class)
                    ->resolveDependencies(),
                MixedB::class => ClassDefinition::singleton(MixedB::class)
                    ->addConstructorArgumentFromClass(MixedD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true)
                    ->increaseReferenceCount("", true),
                MixedD::class => ClassDefinition::singleton(MixedD::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true)
                    ->increaseReferenceCount("", true),
                MixedC::class => ClassDefinition::singleton(MixedC::class)
                    ->addPropertyFromClass("b", MixedB::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolvePrototypeDependency(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            ConstructorD::class,
            [
                ConstructorD::class => DefinitionHint::prototype(ConstructorD::class),
            ]
        );

        $definitions = $dependencyResolver->resolveEntryPoints();

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                ConstructorD::class => ClassDefinition::prototype(ConstructorD::class, true)
                    ->resolveDependencies(),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolveAliasedDependency(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            ConstructorC::class,
            [
                ConstructorD::class => new DefinitionHint(ConstructorE::class),
            ]
        );

        $definitions = $dependencyResolver->resolveEntryPoints();

        $this->assertEquals(
            [
                ContainerInterface::class => ReferenceDefinition::singleton(ContainerInterface::class, "", true),
                "" => new SelfDefinition(""),
                ConstructorC::class => ClassDefinition::singleton(ConstructorC::class, true)
                    ->addConstructorArgumentFromClass(ConstructorD::class)
                    ->resolveDependencies(),
                ConstructorE::class => ClassDefinition::singleton(ConstructorE::class)
                    ->resolveDependencies()
                    ->increaseReferenceCount("", true),
                ConstructorD::class => ReferenceDefinition::singleton(ConstructorD::class, ConstructorE::class)
                    ->increaseReferenceCount("", true),
            ],
            $definitions
        );
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenInexistentClass(): void
    {
        $dependencyResolver = $this->createDependencyResolver("InexistentClass");

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenPropertyWithoutTypeHint(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionA::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenPropertyWithoutDefaultValueWithScalarTypeHint(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionB::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenConstructorParameterWithoutDefaultValueWithScalarTypeHint(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionC::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenWhenConstructorParameterWithoutDefaultValueWithScalarDocBlock(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionD::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenWhenPropertyWithoutTypeInfo(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionE::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @test
     */
    public function resolveEntryPointsWhenWhenStaticProperty(): void
    {
        $dependencyResolver = $this->createDependencyResolver(ExceptionF::class);

        $this->expectException(ContainerException::class);

        $dependencyResolver->resolveEntryPoints();
    }

    /**
     * @param DefinitionHintInterface[]|string[] $definitionHints
     */
    private function createDependencyResolver(string $entryPoint, array $definitionHints = []): ContainerDependencyResolver
    {
        return new ContainerDependencyResolver(
            new StubCompilerConfig(
                [
                    new StubContainerConfig([$entryPoint], $definitionHints),
                ]
            )
        );
    }
}
