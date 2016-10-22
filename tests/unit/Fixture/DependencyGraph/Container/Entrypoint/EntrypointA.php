<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint;

use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassD;

class EntrypointA
{
    public function __construct(ClassD $d)
    {
    }
}
