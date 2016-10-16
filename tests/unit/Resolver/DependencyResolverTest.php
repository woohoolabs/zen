<?php
namespace WoohooLabs\Dicone\Tests\Unit\Resolver;

use PHPUnit_Framework_TestCase;
use WoohooLabs\Dicone\Compiler\CompilationConfig;
use WoohooLabs\Dicone\Definition\DefinitionItem;
use WoohooLabs\Dicone\Resolver\DependencyResolver;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation\AnnotationA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation\AnnotationB;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation\AnnotationC;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation\AnnotationD;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Annotation\AnnotationE;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Constructor\ConstructorA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Constructor\ConstructorB;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Constructor\ConstructorC;
use WoohooLabs\Dicone\Tests\Unit\Fixture\Constructor\ConstructorD;

class DependencyResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function resolveConstructorDependencies()
    {
        $dependencyResolver = new DependencyResolver(new CompilationConfig(true, false));

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
            $dependencyResolver->getDependencyGraph()
        );
    }

    /**
     * @test
     */
    public function resolveAnnotationDependencies()
    {
        $dependencyResolver = new DependencyResolver(new CompilationConfig(false, true));

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
            $dependencyResolver->getDependencyGraph()
        );
    }
}
