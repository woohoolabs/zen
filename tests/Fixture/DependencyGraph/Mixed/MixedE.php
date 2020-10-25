<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed;

use stdClass;

class MixedE
{
    public function __construct(MixedD $mixedD, stdClass $class)
    {
    }
}
