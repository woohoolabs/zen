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
    public function getClassNames()
    {
        $entryPoint = ClassEntryPoint::create(EntryPointA::class);

        $classNames = $entryPoint->getClassNames();

        $this->assertEquals([EntryPointA::class], $classNames);
    }
}
