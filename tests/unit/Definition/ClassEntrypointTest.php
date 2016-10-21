<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Definition\ClassEntryPoint;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointA;

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
