<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\AClass;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\AInterface;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\BClass;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\BInterface;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassEImplementation;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\ClassFImplementation;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\G;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\InterfaceC;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\InterfaceD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\InterfaceE;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\InterfaceF;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Wildcard\InterfaceG;

use function dirname;

class WildcardHintTest extends TestCase
{
    /**
     * @test
     */
    public function getDefinitionHintsWithPrefixPattern(): void
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                AInterface::class => new DefinitionHint(AClass::class),
                BInterface::class => new DefinitionHint(BClass::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function getPrototypeDefinitionHintsWithPrefixPattern(): void
    {
        $wildcardHint = WildcardHint::prototype(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                AInterface::class => DefinitionHint::prototype(AClass::class),
                BInterface::class => DefinitionHint::prototype(BClass::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function getSingletonDefinitionHintsWithPrefixPattern(): void
    {
        $wildcardHint = WildcardHint::singleton(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                AInterface::class => DefinitionHint::singleton(AClass::class),
                BInterface::class => DefinitionHint::singleton(BClass::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function geNonExistentDefinitionHintsWithPrefixPattern(): void
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*NonexistentClass"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEmpty($definitionHints);
    }

    /**
     * @test
     */
    public function getDefinitionHintsWithPostfixPattern(): void
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\Class*"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                InterfaceC::class => new DefinitionHint(ClassC::class),
                InterfaceD::class => new DefinitionHint(ClassD::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function getDefinitionHintsWithInfixPattern(): void
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\Class*Implementation"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                InterfaceE::class => new DefinitionHint(ClassEImplementation::class),
                InterfaceF::class => new DefinitionHint(ClassFImplementation::class),
            ],
            $definitionHints
        );
    }

    /**
     * @test
     */
    public function geNonExistentDefinitionHintsWithOnlyPattern(): void
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Fixture\\DependencyGraph\\Wildcard\\*"
        );

        $definitionHints = $wildcardHint->getDefinitionHints();

        $this->assertEquals(
            [
                InterfaceG::class => new DefinitionHint(G::class),
            ],
            $definitionHints
        );
    }

    private function getSourcePath(): string
    {
        return dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Wildcard";
    }
}
