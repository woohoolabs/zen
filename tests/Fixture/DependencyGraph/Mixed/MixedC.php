<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Zen\Attribute\Inject;

class MixedC
{
    #[Inject]
    /** @var MixedB */
    private $b;
}
