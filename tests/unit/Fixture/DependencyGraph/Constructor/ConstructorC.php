<?php
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
