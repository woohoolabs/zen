<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Dicone\Annotation\Inject;

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
