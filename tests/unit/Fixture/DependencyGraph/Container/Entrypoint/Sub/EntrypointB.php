<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\Sub;

use WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

class EntrypointB
{
    /**
     * @Inject
     * @var ClassC
     */
    private $c;
}
