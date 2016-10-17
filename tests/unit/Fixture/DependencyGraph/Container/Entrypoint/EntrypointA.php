<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint;

use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassD;

class EntrypointA
{
    public function __construct(ClassD $d)
    {
    }
}
