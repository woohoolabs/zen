<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\EntryPoint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class ClassEntryPointTest extends TestCase
{
    /**
     * @test
     */
    public function notAutoloadedByDefault()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $isAutoloaded = $entryPoint->isAutoloaded();

        $this->assertFalse($isAutoloaded);
    }

    /**
     * @test
     */
    public function autoload()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $entryPoint->autoload();

        $this->assertTrue($entryPoint->isAutoloaded());
    }

    /**
     * @test
     */
    public function notAutoloaded()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $entryPoint->autoload();
        $entryPoint->notAutoloaded();

        $this->assertFalse($entryPoint->isAutoloaded());
    }

    /**
     * @test
     */
    public function notFileBasedByDefault()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $isFileBased = $entryPoint->isFileBased();

        $this->assertFalse($isFileBased);
    }

    /**
     * @test
     */
    public function fileBased()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $entryPoint->fileBased();

        $this->assertTrue($entryPoint->isFileBased());
    }

    /**
     * @test
     */
    public function getClassNames()
    {
        $entryPoint = new ClassEntryPoint(EntryPointA::class);

        $classNames = $entryPoint->getClassNames();

        $this->assertEquals([EntryPointA::class], $classNames);
    }
}
