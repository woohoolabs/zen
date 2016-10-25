<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Container\Definition\ClassDefinition;
use WoohooLabs\Zen\Container\Definition\ReferenceDefinition;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;
use WoohooLabs\Zen\Container\DependencyResolver;
use WoohooLabs\Zen\Exception\ContainerException;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationE;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorE;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedD;
use WoohooLabs\Zen\Tests\Unit\Double\StubCompilerConfig;

class DependencyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveConstructorDependencies()
    {
        $dependencyResolver = $this->createDependencyResolver();
        $dependencyResolver->resolve(ConstructorA::class);

        $this->assertEquals(
            [
                "" => new SelfDefinition(""),
                ConstructorA::class => ClassDefinition::singleton(ConstructorA::class)
                    ->addRequiredConstructorArgument(ConstructorB::class)
                    ->addRequiredConstructorArgument(ConstructorC::class)
                    ->addOptionalConstructorArgument(true)
                    ->addOptionalConstructorArgument(null)
                    ->resolveDependencies(),
                ConstructorB::class => ClassDefinition::singleton(ConstructorB::class)
                    ->resolveDependencies(),
                ConstructorC::class => ClassDefinition::singleton(ConstructorC::class)
                    ->addRequiredConstructorArgument(ConstructorD::class)
                    ->resolveDependencies(),
                ConstructorD::class => ClassDefinition::singleton(ConstructorD::class)
                    ->resolveDependencies(),
            ],
            $dependencyResolver->getDefinitions()
        );
    }

    /**
     * @test
     */
    public function resolvePropertyDependencies()
    {
        $dependencyResolver = $this->createDependencyResolver();

        $dependencyResolver->resolve(AnnotationA::class);

        $this->assertEquals(
            [
                "" => new SelfDefinition(""),
                AnnotationA::class => ClassDefinition::singleton(AnnotationA::class)
                    ->addProperty("b", AnnotationB::class)
                    ->addProperty("c", AnnotationC::class)
                    ->resolveDependencies(),
                AnnotationB::class => ClassDefinition::singleton(AnnotationB::class)
                    ->addProperty("e2", AnnotationE::class)
                    ->addProperty("d", AnnotationD::class)
                    ->resolveDependencies(),
                AnnotationC::class => ClassDefinition::singleton(AnnotationC::class)
                    ->addProperty("e1", AnnotationE::class)
                    ->addProperty("e2", AnnotationE::class)
                    ->resolveDependencies(),
                AnnotationE::class => ClassDefinition::singleton(AnnotationE::class)
                    ->resolveDependencies(),
                AnnotationD::class => ClassDefinition::singleton(AnnotationD::class)
                    ->resolveDependencies(),
            ],
            $dependencyResolver->getDefinitions()
        );
    }

    /**
     * @test
     */
    public function resolveAllDependencies()
    {
        $dependencyResolver = $this->createDependencyResolver();

        $dependencyResolver->resolve(MixedA::class);

        $this->assertEquals(
            [
                "" => new SelfDefinition(""),
                MixedA::class => ClassDefinition::singleton(MixedA::class)
                    ->addRequiredConstructorArgument(MixedB::class)
                    ->addRequiredConstructorArgument(MixedC::class)
                    ->addProperty("d", MixedD::class)
                    ->resolveDependencies(),
                MixedB::class => ClassDefinition::singleton(MixedB::class)
                    ->addRequiredConstructorArgument(MixedD::class)
                    ->resolveDependencies(),
                MixedD::class => ClassDefinition::singleton(MixedD::class)
                    ->resolveDependencies(),
                MixedC::class => ClassDefinition::singleton(MixedC::class)
                    ->addProperty("b", MixedB::class)
                    ->resolveDependencies(),
            ],
            $dependencyResolver->getDefinitions()
        );
    }

    /**
     * @test
     */
    public function resolvePrototypeDependency()
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                ConstructorD::class => DefinitionHint::prototype(ConstructorD::class)
            ]
        );

        $dependencyResolver->resolve(ConstructorD::class);

        $this->assertEquals(
            [
                "" => new SelfDefinition(""),
                ConstructorD::class => ClassDefinition::prototype(ConstructorD::class)
                    ->resolveDependencies(),
            ],
            $dependencyResolver->getDefinitions()
        );
    }

    /**
     * @test
     */
    public function resolveAliasedDependency()
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                ConstructorD::class => new DefinitionHint(ConstructorE::class)
            ]
        );

        $dependencyResolver->resolve(ConstructorC::class);

        $this->assertEquals(
            [
                "" => new SelfDefinition(""),
                ConstructorC::class => ClassDefinition::singleton(ConstructorC::class)
                    ->addRequiredConstructorArgument(ConstructorD::class)
                    ->resolveDependencies(),
                ConstructorE::class => ClassDefinition::singleton(ConstructorE::class)
                    ->resolveDependencies(),
                ConstructorD::class => ReferenceDefinition::singleton(ConstructorD::class, ConstructorE::class),
            ],
            $dependencyResolver->getDefinitions()
        );
    }

    /**
     * @test
     */
    public function throwExceptionOnInexistentClass()
    {
        $dependencyResolver = $this->createDependencyResolver();

        $this->expectException(ContainerException::class);
        $dependencyResolver->resolve("InexistentClass");
    }

    private function createDependencyResolver(array $definitionHints = []): DependencyResolver
    {
        return new DependencyResolver(new StubCompilerConfig(), $definitionHints);
    }
}
