<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

class MixedA
{
    /**
     * @Inject
     * @var MixedD
     */
    private $d;

    /**
     * @param MixedC $c
     */
    public function __construct(MixedB $b, $c)
    {
    }
}
