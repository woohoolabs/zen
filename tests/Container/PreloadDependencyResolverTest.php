<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Container;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Preload\ClassPreload;
use WoohooLabs\Zen\Config\Preload\PreloadConfig;
use WoohooLabs\Zen\Config\Preload\PreloadInterface;
use WoohooLabs\Zen\Container\PreloadDependencyResolver;
use WoohooLabs\Zen\Tests\Double\StubCompilerConfig;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute\AttributeA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute\AttributeB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute\AttributeC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute\AttributeD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Attribute\AttributeE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadB;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadF;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadG;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadH;

use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadI;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Preload\PreloadJ;
use function dirname;

class PreloadDependencyResolverTest extends TestCase
{
    /**
     * @test
     */
    public function resolvePreloadsWhenEmpty(): void
    {
        $dependencyResolver = $this->createDependencyResolver([]);

        $preloads = $dependencyResolver->resolvePreloads();

        $this->assertEmpty($preloads);
    }

    /**
     * @test
     */
    public function resolvePreloadsWhenOnlyConstructor(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                new ClassPreload(ConstructorA::class),
            ]
        );

        $preloads = $dependencyResolver->resolvePreloads();

        $this->assertEquals(
            [
                ConstructorA::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Constructor/ConstructorA.php",
                ConstructorB::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Constructor/ConstructorB.php",
                ConstructorC::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Constructor/ConstructorC.php",
                ConstructorD::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Constructor/ConstructorD.php",
            ],
            $preloads
        );
    }

    /**
     * @test
     */
    public function resolvePreloadsWhenOnlyAnnotation(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                new ClassPreload(AttributeA::class),
            ]
        );

        $preloads = $dependencyResolver->resolvePreloads();

        $this->assertEquals(
            [
                AttributeA::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Attribute/AttributeA.php",
                AttributeB::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Attribute/AttributeB.php",
                AttributeC::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Attribute/AttributeC.php",
                AttributeD::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Attribute/AttributeD.php",
                AttributeE::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Attribute/AttributeE.php",
            ],
            $preloads
        );
    }

    /**
     * @test
     */
    public function resolvePreloadsWhenMixed(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                new ClassPreload(PreloadA::class),
            ]
        );

        $preloads = $dependencyResolver->resolvePreloads();

        $this->assertEquals(
            [
                PreloadA::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadA.php",
                PreloadB::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadB.php",
                PreloadC::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadC.php",
                PreloadD::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadD.php",
                PreloadE::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadE.php",
                PreloadF::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadF.php",
                PreloadG::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadG.php",
                PreloadH::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadH.php",
            ],
            $preloads
        );
    }

    /**
     * @test
     */
    public function resolvePreloadsWhenUnionType(): void
    {
        $dependencyResolver = $this->createDependencyResolver(
            [
                new ClassPreload(PreloadJ::class),
            ]
        );

        $preloads = $dependencyResolver->resolvePreloads();

        $this->assertEquals(
            [
                PreloadF::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadF.php",
                PreloadG::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadG.php",
                PreloadH::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadH.php",
                PreloadI::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadI.php",
                PreloadJ::class => dirname(__DIR__) . "/Fixture/DependencyGraph/Preload/PreloadJ.php",
            ],
            $preloads
        );
    }

    /**
     * @param PreloadInterface[] $preloadedClasses
     */
    private function createDependencyResolver(array $preloadedClasses): PreloadDependencyResolver
    {
        return new PreloadDependencyResolver(
            new StubCompilerConfig(
                [],
                "",
                "",
                false,
                false,
                false,
                PreloadConfig::create()
                    ->setPreloadedClasses($preloadedClasses)
            )
        );
    }
}
