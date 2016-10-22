<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Compiler;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Compiler\CompilerConfig;
use WoohooLabs\Zen\Compiler\DependencyResolver;
use WoohooLabs\Zen\Definition\Definition;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationE;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedB;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedD;

class DependencyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolveConstructorDependencies()
    {
        $dependencyResolver = new DependencyResolver(new CompilerConfig(true, false));

        $dependencyResolver->resolve(ConstructorA::class);

        $this->assertEquals(
            [
                ConstructorA::class => Definition::singleton(ConstructorA::class)
                    ->addRequiredConstructorParam(ConstructorB::class)
                    ->addRequiredConstructorParam(ConstructorC::class)
                    ->addOptionalConstructorParam(true)
                    ->addOptionalConstructorParam(null),
                ConstructorB::class => Definition::singleton(ConstructorB::class),
                ConstructorC::class => Definition::singleton(ConstructorC::class)
                    ->addRequiredConstructorParam(ConstructorD::class),
                ConstructorD::class => Definition::singleton(ConstructorD::class),
            ],
            $dependencyResolver->getDefinitionItems()
        );
    }

    /**
     * @test
     */
    public function resolveAnnotationDependencies()
    {
        $dependencyResolver = new DependencyResolver(new CompilerConfig(false, true));

        $dependencyResolver->resolve(AnnotationA::class);

        $this->assertEquals(
            [
                AnnotationA::class => Definition::singleton(AnnotationA::class)
                    ->addProperty("b", AnnotationB::class)
                    ->addProperty("c", AnnotationC::class),
                AnnotationB::class => Definition::singleton(AnnotationB::class)
                    ->addProperty("e2", AnnotationE::class)
                    ->addProperty("d", AnnotationD::class),
                AnnotationC::class => Definition::singleton(AnnotationC::class)
                    ->addProperty("e1", AnnotationE::class)
                    ->addProperty("e2", AnnotationE::class),
                AnnotationE::class => Definition::singleton(AnnotationE::class),
                AnnotationD::class => Definition::singleton(AnnotationD::class),
            ],
            $dependencyResolver->getDefinitionItems()
        );
    }

    /**
     * @test
     */
    public function resolveMixedDependencies()
    {
        $dependencyResolver = new DependencyResolver(new CompilerConfig(true, true));

        $dependencyResolver->resolve(MixedA::class);

        $this->assertEquals(
            [
                MixedA::class => Definition::singleton(MixedA::class)
                    ->addRequiredConstructorParam(MixedB::class)
                    ->addRequiredConstructorParam(MixedC::class)
                    ->addProperty("d", MixedD::class),
                MixedB::class => Definition::singleton(MixedB::class)
                    ->addRequiredConstructorParam(MixedD::class),
                MixedD::class => Definition::singleton(MixedD::class),
                MixedC::class => Definition::singleton(MixedC::class)
                    ->addProperty("b", MixedB::class),
            ],
            $dependencyResolver->getDefinitionItems()
        );
    }
}
