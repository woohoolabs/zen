<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\EntryPoint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\WildcardEntryPoint;
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

class WildcardEntryPointTest extends TestCase
{
    /**
     * @test
     */
    public function getOnlyConcreteClassNames()
    {
        $entryPoint = new WildcardEntryPoint($this->getSourcePath());

        $this->assertEquals(
            [
                AClass::class,
                BClass::class,
                ClassC::class,
                ClassD::class,
                ClassEImplementation::class,
                ClassFImplementation::class,
                G::class,
            ],
            $entryPoint->getClassNames(),
            '',
            0.0,
            10,
            true
        );
    }

    /**
     * @test
     */
    public function isAutoloaded()
    {
        $entryPoint = WildcardEntryPoint::create($this->getSourcePath())->autoload();

        $this->assertTrue($entryPoint->isAutoloaded());
    }

    /**
     * @test
     */
    public function getAllClassNames()
    {
        $entryPoint = new WildcardEntryPoint($this->getSourcePath(), false);

        $this->assertEquals(
            [
                AClass::class,
                AInterface::class,
                BClass::class,
                BInterface::class,
                ClassC::class,
                ClassD::class,
                ClassEImplementation::class,
                ClassFImplementation::class,
                G::class,
                InterfaceC::class,
                InterfaceD::class,
                InterfaceE::class,
                InterfaceF::class,
                InterfaceG::class,
            ],
            $entryPoint->getClassNames(),
            '',
            0.0,
            10,
            true
        );
    }

    private function getSourcePath(): string
    {
        return dirname(__DIR__, 2) . "/Fixture/DependencyGraph/Wildcard";
    }
}