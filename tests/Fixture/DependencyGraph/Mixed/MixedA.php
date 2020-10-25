<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Zen\Annotation\Inject;

class MixedA
{
    #[Inject]
    /** @var MixedD */
    private $d;

    /**
     * @param MixedC $c
     */
    public function __construct(MixedB $b, $c)
    {
    }
}
