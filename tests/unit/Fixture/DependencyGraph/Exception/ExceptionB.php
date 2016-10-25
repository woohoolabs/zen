<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Exception;

class ExceptionB
{
    /**
     * @Inject
     * @var string
     */
    private $a;
}
