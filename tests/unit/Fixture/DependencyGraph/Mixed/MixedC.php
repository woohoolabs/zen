<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

class MixedC
{
    /**
     * @Inject
     * @var MixedB
     */
    private $b;
}
