<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Config\Hint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\WildcardHint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\ClassC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\ClassD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\AClass;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\AInterface;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\BClass;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\BInterface;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\ClassEImplementation;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\ClassFImplementation;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\G;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\InterfaceC;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\InterfaceD;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\InterfaceE;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\InterfaceF;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Wildcard\InterfaceG;

class WildcardHintTest extends TestCase
{
    /**
     * @test
     */
    public function getDefinitionHintsWithPrefixPattern()
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $this->assertEquals(
            [
                AInterface::class => new DefinitionHint(AClass::class),
                BInterface::class => new DefinitionHint(BClass::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function getPrototypeDefinitionHintsWithPrefixPattern()
    {
        $sourcePath = dirname(__DIR__, 2) . "/Fixture/WildcardHint";

        $wildcardHint = WildcardHint::prototype(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $this->assertEquals(
            [
                AInterface::class => DefinitionHint::prototype(AClass::class),
                BInterface::class => DefinitionHint::prototype(BClass::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function getSingletonDefinitionHintsWithPrefixPattern()
    {
        $wildcardHint = WildcardHint::singleton(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Class"
        );

        $this->assertEquals(
            [
                AInterface::class => DefinitionHint::singleton(AClass::class),
                BInterface::class => DefinitionHint::singleton(BClass::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function geNonExistentDefinitionHintsWithPrefixPattern()
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*Interface",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*NonexistentClass"
        );

        $this->assertEquals(
            [],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function getDefinitionHintsWithPostfixPattern()
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\Class*"
        );

        $this->assertEquals(
            [
                InterfaceC::class => new DefinitionHint(ClassC::class),
                InterfaceD::class => new DefinitionHint(ClassD::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function getDefinitionHintsWithInfixPattern()
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\Class*Implementation"
        );

        $this->assertEquals(
            [
                InterfaceE::class => new DefinitionHint(ClassEImplementation::class),
                InterfaceF::class => new DefinitionHint(ClassFImplementation::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    /**
     * @test
     */
    public function geNonExistentDefinitionHintsWithOnlyPattern()
    {
        $wildcardHint = new WildcardHint(
            $this->getSourcePath(),
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\Interface*",
            "WoohooLabs\\Zen\Tests\\Unit\\Fixture\\DependencyGraph\\Wildcard\\*"
        );

        $this->assertEquals(
            [
                InterfaceG::class => new DefinitionHint(G::class),
            ],
            $wildcardHint->getDefinitionHints()
        );
    }

    private function getSourcePath(): string
    {
        return dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Wildcard";
    }
}
