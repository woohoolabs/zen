<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Definition\DirectoryWildcardEntryPoint;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointA;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointC1;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointC2;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointEInterface;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Entrypoint\EntrypointFTrait;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\EntrypointD1\EntrypointD1;
use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\EntrypointD2\EntrypointD2;

class DirectoryWildcardEntrypointTest extends TestCase
{
    /**
     * @test
     */
    public function getDefinitionItems()
    {
        $entrypoint = new DirectoryWildcardEntryPoint(realpath(__DIR__ . "/../Fixture/DependencyGraph/Entrypoint"));

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
