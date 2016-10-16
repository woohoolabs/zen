<?php
namespace WoohooLabs\Dicone\Tests\Unit\Fixture\Mixed;

use WoohooLabs\Dicone\Resolver\Annotation\Dependency;

class MixedC
{
    /**
     * @Dependency
     * @var MixedB
     */
    private $b;
}
