<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed;

class MixedB
{
    public function __construct(MixedD $d)
    {
    }
}
