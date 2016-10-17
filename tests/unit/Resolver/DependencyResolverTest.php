<?php
namespace WoohooLabs\Dicone\Tests\Unit\Resolver;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Compiler\CompilerConfig;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Resolver\DependencyResolver;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationB;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationC;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationD;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Annotation\AnnotationE;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorC;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedB;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedC;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed\MixedD;

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
                ConstructorA::class => DefinitionItem::singleton(ConstructorA::class)
                    ->addRequiredConstructorParam(ConstructorB::class)
                    ->addRequiredConstructorParam(ConstructorC::class)
                    ->addOptionalConstructorParam(true)
                    ->addOptionalConstructorParam(null),
                ConstructorB::class => DefinitionItem::singleton(ConstructorB::class),
                ConstructorC::class => DefinitionItem::singleton(ConstructorC::class)
                    ->addRequiredConstructorParam(ConstructorD::class),
                ConstructorD::class => DefinitionItem::singleton(ConstructorD::class),
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
                AnnotationA::class => DefinitionItem::singleton(AnnotationA::class)
                    ->addProperty("b", AnnotationB::class)
                    ->addProperty("c", AnnotationC::class),
                AnnotationB::class => DefinitionItem::singleton(AnnotationB::class)
                    ->addProperty("e2", AnnotationE::class)
                    ->addProperty("d", AnnotationD::class),
                AnnotationC::class => DefinitionItem::singleton(AnnotationC::class)
                    ->addProperty("e1", AnnotationE::class)
                    ->addProperty("e2", AnnotationE::class),
                AnnotationE::class => DefinitionItem::singleton(AnnotationE::class),
                AnnotationD::class => DefinitionItem::singleton(AnnotationD::class),
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
                MixedA::class => DefinitionItem::singleton(MixedA::class)
                    ->addRequiredConstructorParam(MixedB::class)
                    ->addRequiredConstructorParam(MixedC::class)
                    ->addProperty("d", MixedD::class),
                MixedB::class => DefinitionItem::singleton(MixedB::class)
                    ->addRequiredConstructorParam(MixedD::class),
                MixedD::class => DefinitionItem::singleton(MixedD::class),
                MixedC::class => DefinitionItem::singleton(MixedC::class)
                    ->addProperty("b", MixedB::class),
            ],
            $dependencyResolver->getDefinitionItems()
        );
    }
}
