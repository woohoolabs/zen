<?php
namespace WoohooLabs\Dicone\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Dicone\Definition\DirectoryWildcardEntrypoint;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointA;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointC1;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointC2;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointEInterface;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointFTrait;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\EntrypointD1\EntrypointD1;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\EntrypointD2\EntrypointD2;

class DirectoryWildcardEntrypointTest extends TestCase
{
    /**
     * @test
     */
    public function getDefinitionItems()
    {
        $entrypoint = new DirectoryWildcardEntrypoint(realpath(__DIR__ . "/../Fixture/DependencyGraph/Entrypoint"));

        $this->assertEquals(
            [
                EntrypointA::class,
                "EntrypointB",
                EntrypointC1::class,
                EntrypointC2::class,
                EntrypointD1::class,
                EntrypointD2::class,
                EntrypointEInterface::class,
                EntrypointFTrait::class,
            ],
            $entrypoint->getClassNames()
        );
    }
}
