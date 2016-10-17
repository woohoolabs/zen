<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class MixedC
{
    /**
     * @Dependency
     * @var MixedB
     */
    private $b;
}
