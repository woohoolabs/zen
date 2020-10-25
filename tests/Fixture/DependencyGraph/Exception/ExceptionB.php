<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Exception;

class ExceptionB
{
    /**
     * @Inject
     * @var string
     */
    private $a;
}
