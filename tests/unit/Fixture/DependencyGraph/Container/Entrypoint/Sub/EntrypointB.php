<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\Sub;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

class EntrypointB
{
    /**
     * @Dependency
     * @var ClassC
     */
    private $c;
}
