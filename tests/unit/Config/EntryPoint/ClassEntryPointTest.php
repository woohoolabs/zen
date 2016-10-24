<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Config\EntryPoint;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntryPointA;

class ClassEntryPointTest extends TestCase
{
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
