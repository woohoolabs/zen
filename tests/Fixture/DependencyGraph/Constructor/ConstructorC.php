<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor;

class ConstructorC
{
    /**
     * @param ConstructorE $d
     */
    public function __construct(ConstructorD $d)
    {
    }
}
