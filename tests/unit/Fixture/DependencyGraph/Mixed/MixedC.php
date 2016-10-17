<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Mixed;

use WoohooLabs\Dicone\Annotation\Inject;

class MixedC
{
    /**
     * @Inject
     * @var MixedB
     */
    private $b;
}
