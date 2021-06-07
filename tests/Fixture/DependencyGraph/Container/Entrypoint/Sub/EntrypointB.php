<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Container\Entrypoint\Sub;

use WoohooLabs\Zen\Attribute\Inject;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Container\ClassC;

class EntrypointB
{
    #[Inject]
    /** @var ClassC */
    private $c;
}
