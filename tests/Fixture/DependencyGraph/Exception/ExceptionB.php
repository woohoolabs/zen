<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception;

use WoohooLabs\Zen\Annotation\Inject;

class ExceptionB
{
    #[Inject]
    /** @var string */
    private $a;
}
