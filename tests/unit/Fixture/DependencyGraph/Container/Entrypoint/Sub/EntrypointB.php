<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\Entrypoint\Sub;

use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Container\ClassC;

class EntrypointB
{
    /**
     * @Inject
     * @var ClassC
     */
    private $c;
}
