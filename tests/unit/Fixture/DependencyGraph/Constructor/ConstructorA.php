<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Fixture\DependencyGraph\Constructor;

class ConstructorA
{
    /**
     * @param ConstructorC $c
     * @param bool $d
     * @param null $e
     */
    public function __construct(ConstructorB $b, $c, bool $d = true, string $e = null)
    {
    }
}
