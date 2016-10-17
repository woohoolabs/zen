<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class MixedA
{
    /**
     * @Dependency
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
