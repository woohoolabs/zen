<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Definition\ClassEntryPoint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointA;

class ClassEntrypointTest extends TestCase
{
    /**
     * @test
     */
    public function getDefinitionItems()
    {
        $entrypoint = new ClassEntryPoint(EntrypointA::class);

        $this->assertEquals(
            [
                EntrypointA::class
            ],
            $entrypoint->getClassNames()
        );
    }
}
