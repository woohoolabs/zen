<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception;

use WoohooLabs\Zen\Attribute\Inject;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorE;

class ExceptionH
{
    public function __construct(ConstructorD|ConstructorE $a)
    {
    }
}
