<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Mixed;

class MixedC
{
    /**
     * @Inject
     * @var MixedB
     */
    private $b;
}
