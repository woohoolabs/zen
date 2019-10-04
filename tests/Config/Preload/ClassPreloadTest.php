<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Config\Preload;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Config\Preload\ClassPreload;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;

class ClassPreloadTest extends TestCase
{
    /**
     * @test
     */
    public function getClassNames(): void
    {
        $preload = ClassPreload::create(EntryPointA::class);

        $classNames = $preload->getClassNames();

        $this->assertEquals([EntryPointA::class], $classNames);
    }
}
