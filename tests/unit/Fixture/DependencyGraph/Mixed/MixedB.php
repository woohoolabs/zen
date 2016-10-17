<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

class MixedB
{
    public function __construct(MixedD $d)
    {
    }
}
