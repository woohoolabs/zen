<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Config\EntryPoint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class ClassEntryPointTest extends TestCase
{
    /**
     * @test
     */
    public function notAutoloadedByDefault()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $this->assertFalse($entryPoint->isAutoloaded());
    }

    /**
     * @test
     */
    public function autoload()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class)
            ->autoload();

        $this->assertTrue($entryPoint->isAutoloaded());
    }

    /**
     * @test
     */
    public function getDefinitionItems()
    {
        $entryPoint = new ClassEntryPoint(EntryPointA::class);

        $this->assertEquals(
            [
                EntryPointA::class
            ],
            $entryPoint->getClassNames()
        );
    }
}
