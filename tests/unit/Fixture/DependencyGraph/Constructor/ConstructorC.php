<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Tests\Unit\Fixture\DependencyGraph\Constructor;

class ConstructorC
{
    /**
     * @param ConstructorE $d
     */
    public function __construct(ConstructorD $d)
    {
    }
}
